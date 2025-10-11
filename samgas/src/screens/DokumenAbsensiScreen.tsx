import React, { useState, useMemo } from 'react';
import { StyleSheet, View, ScrollView, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Text, Icon, TextInput } from 'react-native-paper';
import { RootStackParamList } from '../navigation/types';
import BottomNavBar from '../components/BottomNavBar';
import ConfirmationModal from '../components/ConfirmationModal';


type DokumenAbsensiScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'DokumenAbsensi'>;

// --- DUMMY DATA ---
const absensiData = [
  {
    id: 1,
    kelas: '1',
  },
  {
    id: 2,
    kelas: '2',
  },
  {
    id: 3,
    kelas: '3',
  },
];



// --- KOMPONEN ---

const Header = ({ onBackPress, onPromotePress }: { onBackPress: () => void, onPromotePress: () => void }) => (
  <View style={styles.header}>
    <View style={styles.headerTop}>
      <TouchableOpacity onPress={onBackPress}>
        <Icon source="arrow-left" size={28} color="#FFFFFF" />
      </TouchableOpacity>
      <Text style={styles.headerTitle}>Dokumen Absensi</Text>
      <View style={{ width: 28 }} />
    </View>
    <UserInfo onPromotePress={onPromotePress} />
  </View>
);

const UserInfo = ({ onPromotePress }: { onPromotePress: () => void }) => (
    <View style={styles.userInfo}>
        <View>
            <Text style={styles.userInfoRole}>Role: super-user</Text>
        </View>
        <TouchableOpacity style={styles.promoteButton} onPress={onPromotePress}>
            <Text style={styles.promoteButtonText}>Naik Kelas</Text>
        </TouchableOpacity>
    </View>
);

const SearchBar = () => {
  const [searchQuery, setSearchQuery] = React.useState('');
  return (
    <View style={styles.searchContainer}>
      <TextInput
        label="Cari Dokumen"
        value={searchQuery}
        onChangeText={setSearchQuery}
        style={styles.searchInput}
        mode="outlined"
        left={<TextInput.Icon icon="magnify" />}
      />
    </View>
  );
};

const DokumenItem = ({
  item,
  isLastItem,
  onDownload,
  onDelete
}: {
  item: typeof absensiData[0],
  isLastItem: boolean,
  onDownload: (item: any) => void,
  onDelete: (item: any) => void
}) => (
  <View>
    <View style={styles.docItem}>
      <View style={styles.docHeader}>
        <Text style={styles.docTitle}>kelas: {item.kelas}</Text>
        <View style={styles.iconContainer}>
          <TouchableOpacity onPress={() => onDownload(item)}>
            <Icon source="download-outline" size={24} color="#007AFF" />
          </TouchableOpacity>
          <TouchableOpacity style={{ marginLeft: 16 }} onPress={() => onDelete(item)}>
            <Icon source="trash-can-outline" size={24} color="#FF3B30" />
          </TouchableOpacity>
        </View>
      </View>
    </View>
    {!isLastItem && <View style={styles.separator} />}
  </View>
);

// --- SCREEN UTAMA ---

const DokumenAbsensiScreen = () => {
  const navigation = useNavigation<DokumenAbsensiScreenNavigationProp>();
  const [modalVisible, setModalVisible] = useState(false);
  const [modalAction, setModalAction] = useState<'download' | 'delete' | null>(null);
  const [selectedItem, setSelectedItem] = useState<any>(null);
  const [modalStatus, setModalStatus] = useState<'confirm' | 'loading' | 'success' | 'error'>('confirm');

  // State for the two-step promotion confirmation
  const [promoteModalVisible, setPromoteModalVisible] = useState(false);
  const [promoteStep, setPromoteStep] = useState(1);
  const [promoteStatus, setPromoteStatus] = useState<'confirm' | 'loading' | 'success' | 'error'>('confirm');

  const handleDownload = (item: any) => {
    setSelectedItem(item);
    setModalAction('download');
    setModalStatus('confirm');
    setModalVisible(true);
  };

  const handleDelete = (item: any) => {
    setSelectedItem(item);
    setModalAction('delete');
    setModalStatus('confirm');
    setModalVisible(true);
  };

  const handleConfirm = () => {
    setModalStatus('loading');
    setTimeout(() => {
      const isSuccess = Math.random() < 0.5;
      if (isSuccess) {
        setModalStatus('success');
      } else {
        setModalStatus('error');
      }
    }, 2000);
  };

  const handleClose = () => {
    setModalVisible(false);
    setTimeout(() => {
        setSelectedItem(null);
        setModalAction(null);
        setModalStatus('confirm');
    }, 300);
  };

  // --- Handlers for Promote Class action ---
  const handlePromoteClick = () => {
    setPromoteStatus('confirm');
    setPromoteStep(1);
    setPromoteModalVisible(true);
  };

  const handlePromoteConfirm = () => {
    if (promoteStep === 1) {
      // First confirmation, advance to the second step
      setPromoteStep(2);
    } else {
      // Second confirmation, execute the action
      setPromoteStatus('loading');
      setTimeout(() => {
        const isSuccess = Math.random() < 0.5;
        if (isSuccess) {
          setPromoteStatus('success');
        } else {
          setPromoteStatus('error');
        }
      }, 2000);
    }
  };

  const handlePromoteClose = () => {
    setPromoteModalVisible(false);
    setTimeout(() => {
      setPromoteStep(1); // Reset to first step for next time
      setPromoteStatus('confirm');
    }, 300);
  };

  const modalStrings = useMemo(() => {
    if (!selectedItem) return { title: '', message: '', successMessage: '', errorMessage: '' };
    const docName = `Dokumen Absensi Kelas "${selectedItem.kelas}"`;
    if (modalAction === 'download') {
      return {
        title: 'Konfirmasi Unduh',
        message: `Apakah Anda yakin ingin mengunduh ${docName}?`,
        successMessage: `${docName} BERHASIL diunduh.`,
        errorMessage: `${docName} GAGAL diunduh.`,
      };
    }
    if (modalAction === 'delete') {
      return {
        title: 'Konfirmasi Hapus',
        message: `Apakah Anda yakin ingin menghapus ${docName}?`,
        successMessage: `${docName} BERHASIL dihapus.`,
        errorMessage: `${docName} GAGAL dihapus.`,
      };
    }
    return { title: '', message: '', successMessage: '', errorMessage: '' };
  }, [selectedItem, modalAction]);

  return (
    <SafeAreaView style={styles.container}>
      <Header onBackPress={() => navigation.goBack()} onPromotePress={handlePromoteClick} />
      <SearchBar />
      <ScrollView contentContainerStyle={styles.scrollViewContent}>
        {absensiData.map((item, index) => (
          <DokumenItem
            key={item.id}
            item={item}
            isLastItem={index === absensiData.length - 1}
            onDownload={handleDownload}
            onDelete={handleDelete}
          />
        ))}
      </ScrollView>
      <BottomNavBar />
      {selectedItem && (
        <ConfirmationModal
          visible={modalVisible}
          status={modalStatus}
          onConfirm={modalStatus === 'confirm' ? handleConfirm : handleClose}
          onCancel={handleClose}
          onRetry={handleConfirm}
          title={modalStrings.title}
          message={modalStrings.message}
          messageColor={modalAction === 'delete' && modalStatus === 'confirm' ? 'red' : 'black'}
          successMessage={modalStrings.successMessage}
          errorMessage={modalStrings.errorMessage}
          documentType="Dokumen"
        />
      )}

      {/* Modal for Promote Class Action */}
      <ConfirmationModal
        visible={promoteModalVisible}
        status={promoteStatus}
        onConfirm={promoteStatus === 'confirm' ? handlePromoteConfirm : handlePromoteClose}
        onCancel={handlePromoteClose}
        onRetry={handlePromoteConfirm}
        title="Konfirmasi Kenaikan Kelas"
        message="Apakah Anda yakin ingin \'menaikan kelas\'? Ini berarti akan menghapus dokumen kelas 6!!!"
        messageColor={promoteStep === 2 ? 'red' : 'black'} // Red color for the second confirmation
        successMessage="Proses kenaikan kelas BERHASIL dijalankan."
        errorMessage="Proses kenaikan kelas GAGAL. Silakan coba lagi."
        documentType="Proses"
      />
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
    backgroundColor: '#007AFF',
    paddingTop: 10,
    paddingBottom: 10,
    paddingHorizontal: 15,
  },
  headerTop: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 10,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  // User Info
  userInfo: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  userInfoRole: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  promoteButton: {
    backgroundColor: '#FF9500', // A distinct color for the action
    paddingVertical: 6,
    paddingHorizontal: 12,
    borderRadius: 8,
  },
  promoteButtonText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: 'bold',
  },
  // Search
  searchContainer: {
    paddingHorizontal: 16,
    paddingTop: 16,
  },
  searchInput: {
    backgroundColor: '#FFFFFF',
  },
  // ScrollView
  scrollViewContent: {
    paddingHorizontal: 16,
    paddingTop: 10,
    paddingBottom: 80, // Space for footer
  },
  // RPP Item
  docItem: {
    paddingVertical: 12,
  },
  docHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  docTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#000',
  },
  iconContainer: {
    flexDirection: 'row',
  },

  separator: {
    height: 1,
    backgroundColor: '#D1D1D6',
  },
});

export default DokumenAbsensiScreen;
