import React, { useState, useMemo } from 'react';
import { StyleSheet, View, ScrollView, TouchableOpacity, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Text, Icon, TextInput } from 'react-native-paper';
import { RootStackParamList } from '../navigation/types';
import BottomNavBar from '../components/BottomNavBar';
import ConfirmationModal from '../components/ConfirmationModal';

type HapusMuridScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'HapusMurid'>;

// --- DUMMY DATA ---
const initialMuridData = [
  { id: 1, nama: 'Nomor 2', kelas: 'V' },
  { id: 2, nama: 'Ujang', kelas: 'V' },
  { id: 3, nama: 'NoName', kelas: 'V' },
  { id: 4, nama: 'Anonymous', kelas: 'V' },
  { id: 5, nama: 'Antena-kipas', kelas: 'V' },
  { id: 6, nama: 'Shadow', kelas: 'V' },
];

// --- KOMPONEN ---

const Header = ({ onBackPress }: { onBackPress: () => void }) => (
  <View style={styles.header}>
    <TouchableOpacity onPress={onBackPress} style={styles.backButton}>
      <Icon source="arrow-left" size={28} color="#FFFFFF" />
    </TouchableOpacity>
    <Text style={styles.headerTitle}>Hapus Murid</Text>
    <View style={{ width: 28 }} />
  </View>
);

const SearchBar = ({ searchQuery, setSearchQuery }: { searchQuery: string, setSearchQuery: (query: string) => void }) => {
  return (
    <View style={styles.searchContainer}>
      <TextInput
        label="Cari nama siswa"
        value={searchQuery}
        onChangeText={setSearchQuery}
        style={styles.searchInput}
        mode="outlined"
        left={<TextInput.Icon icon="magnify" />}
      />
    </View>
  );
};

const MuridItem = ({
  item,
  isLastItem,
  onDelete
}: {
  item: typeof initialMuridData[0],
  isLastItem: boolean,
  onDelete: (item: any) => void
}) => (
  <View>
    <View style={styles.muridItem}>
      <View style={styles.muridInfo}>
        <Text style={styles.muridName}>Nama: {item.nama}</Text>
        <Text style={styles.muridKelas}>Kelas: {item.kelas}</Text>
      </View>
      <TouchableOpacity style={styles.deleteButton} onPress={() => onDelete(item)}>
        <Icon source="trash-can-outline" size={24} color="#FF3B30" />
      </TouchableOpacity>
    </View>
    {!isLastItem && <View style={styles.separator} />}
  </View>
);

// --- SCREEN UTAMA ---

const HapusMuridScreen = () => {
  const navigation = useNavigation<HapusMuridScreenNavigationProp>();
  const [muridData, setMuridData] = useState(initialMuridData);
  const [searchQuery, setSearchQuery] = useState('');
  
  const [modalVisible, setModalVisible] = useState(false);
  const [modalStatus, setModalStatus] = useState<'confirm' | 'loading' | 'success' | 'error'>('confirm');
  const [selectedMurid, setSelectedMurid] = useState<any>(null);
  const [deleteStep, setDeleteStep] = useState(1);

  const handleDelete = (murid: any) => {
    setSelectedMurid(murid);
    setModalStatus('confirm');
    setDeleteStep(1); // Reset to step 1
    setModalVisible(true);
  };

  const handleConfirmDelete = () => {
    if (deleteStep === 1) {
      // First confirmation, advance to the second step
      setDeleteStep(2);
    } else {
      // Second confirmation, execute the action
      setModalStatus('loading');
      setTimeout(() => {
        const isSuccess = Math.random() < 0.5; // 80% chance of success
        if (isSuccess) {
          setModalStatus('success');
          setMuridData(prevData => prevData.filter(m => m.id !== selectedMurid.id));
        } else {
          setModalStatus('error');
        }
      }, 1500);
    }
  };

  const handleCloseModal = () => {
    setModalVisible(false);
    setTimeout(() => {
        setSelectedMurid(null);
        setModalStatus('confirm');
        setDeleteStep(1); // Reset step on close
    }, 300);
  };

  const filteredMuridData = useMemo(() => 
    muridData.filter(murid => 
      murid.nama.toLowerCase().includes(searchQuery.toLowerCase())
    ), [muridData, searchQuery]);

  const modalStrings = useMemo(() => {
    if (!selectedMurid) return { title: '', message: '', successMessage: '', errorMessage: '' };
    const muridInfo = `murid "${selectedMurid.nama}" dari kelas "${selectedMurid.kelas}"`;
    
    if (deleteStep === 1) {
      return {
          title: 'Konfirmasi Hapus',
          message: `Apakah Anda yakin ingin menghapus ${muridInfo}?`,
          successMessage: `Data ${muridInfo} BERHASIL dihapus.`,
          errorMessage: `Data ${muridInfo} GAGAL dihapus. Silakan coba lagi.`,
      };
    }
    // Step 2
    return {
        title: 'HAPUS PERMANEN',
        message: `Anda akan MENGHAPUS ${muridInfo} secara permanen. Tindakan ini tidak dapat dibatalkan.`,
        successMessage: `Data ${muridInfo} BERHASIL dihapus.`,
        errorMessage: `Data ${muridInfo} GAGAL dihapus. Silakan coba lagi.`,
    };
  }, [selectedMurid, deleteStep]);

  return (
    <SafeAreaView style={styles.container}>
      <Header onBackPress={() => navigation.goBack()} />
      <SearchBar searchQuery={searchQuery} setSearchQuery={setSearchQuery} />
      <ScrollView contentContainerStyle={styles.scrollViewContent}>
        {filteredMuridData.length > 0 ? (
          filteredMuridData.map((item, index) => (
            <MuridItem
              key={item.id}
              item={item}
              isLastItem={index === filteredMuridData.length - 1}
              onDelete={handleDelete}
            />
          ))
        ) : (
          <Text style={styles.noResultsText}>Tidak ada murid yang cocok dengan pencarian Anda.</Text>
        )}
      </ScrollView>
      <BottomNavBar />

      {selectedMurid && (
        <ConfirmationModal
          visible={modalVisible}
          status={modalStatus}
          onConfirm={modalStatus === 'confirm' ? handleConfirmDelete : handleCloseModal}
          onCancel={handleCloseModal}
          onRetry={handleConfirmDelete}
          title={modalStrings.title}
          message={modalStrings.message}
          messageColor={deleteStep === 2 ? 'red' : 'black'}
          successMessage={modalStrings.successMessage}
          errorMessage={modalStrings.errorMessage}
          documentType="Data Murid"
        />
      )}
    </SafeAreaView>
  );
};

// --- STYLES ---

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F2F2F7',
  },
  // Header
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#007AFF',
    paddingVertical: 12,
    paddingHorizontal: 15,
  },
  backButton: {
    padding: 5,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  // Search
  searchContainer: {
    paddingHorizontal: 16,
    paddingTop: 16,
    paddingBottom: 8,
  },
  searchInput: {
    backgroundColor: '#FFFFFF',
  },
  // ScrollView
  scrollViewContent: {
    paddingHorizontal: 16,
    paddingBottom: 80, // Space for footer
  },
  // Murid Item
  muridItem: {
    paddingVertical: 12,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 5,
  },
  muridInfo: {
    flex: 1,
  },
  muridName: {
    fontSize: 16,
    fontWeight: '600',
    color: '#000',
    marginBottom: 4,
  },
  muridKelas: {
    fontSize: 14,
    color: '#3C3C43',
  },
  deleteButton: {
    padding: 8,
  },
  separator: {
    height: 1,
    backgroundColor: '#D1D1D6',
    marginVertical: 4,
  },
  noResultsText: {
    textAlign: 'center',
    marginTop: 40,
    fontSize: 16,
    color: '#666',
  },
});

export default HapusMuridScreen;
