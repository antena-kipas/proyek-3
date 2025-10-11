
import React, { useState, useMemo } from 'react';
import { StyleSheet, View, ScrollView, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Text, Icon, TextInput } from 'react-native-paper';
import { RootStackParamList } from '../navigation/types';
import BottomNavBar from '../components/BottomNavBar';
import ConfirmationModal from '../components/ConfirmationModal';

type RekapAbsensiScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'RekapAbsensi'>;

// --- DUMMY DATA ---
const RekapData = [
  { 
    id: 1, 
    bulan: 'Januari'
  },
  { 
    id: 2, 
    bulan: 'Februari'
  },
  { 
    id: 3, 
    bulan: 'Maret'
  },
];

const user = {
    nama: 'Jamal',
    kelas: 'V '
};

// --- KOMPONEN ---

const Header = ({ onBackPress }: { onBackPress: () => void }) => (
  <View style={styles.header}>
    <View style={styles.headerTop}>
      <TouchableOpacity onPress={onBackPress}>
        <Icon source="arrow-left" size={28} color="#FFFFFF" />
      </TouchableOpacity>
      <Text style={styles.headerTitle}>Rekap Absensi</Text>
      <View style={{ width: 28 }} /> 
    </View>
    <UserInfo />
  </View>
);

const UserInfo = () => (
    <View style={styles.userInfoContainer}>
        <View style={styles.userInfoRow}>
            <Text style={styles.userInfoLabel}>Nama</Text>
            <Text style={styles.userInfoValue}>: {user.nama}</Text>
        </View>
        <View style={styles.userInfoRow}>
            <Text style={styles.userInfoLabel}>Kelas</Text>
            <Text style={styles.userInfoValue}>: {user.kelas}</Text>
        </View>
    </View>
);

const SearchBar = () => {
  const [searchQuery, setSearchQuery] = React.useState('');
  return (
    <View style={styles.searchContainer}>
      <TextInput
        label="Cari Dokumen Absensi"
        value={searchQuery}
        onChangeText={setSearchQuery}
        style={styles.searchInput}
        mode="outlined"
        left={<TextInput.Icon icon="magnify" />}
      />
    </View>
  );
};

const RekapAbsensiItem = ({ 
  item, 
  isLastItem,
  onDownload,
  onDelete
}: { 
  item: typeof RekapData[0], 
  isLastItem: boolean,
  onDownload: (item: any) => void,
  onDelete: (item: any) => void
}) => (
  <View>
    <View style={styles.rekapItem}>
      <View style={styles.rekapHeader}>
        <Text style={styles.rekapMonth}>Bulan: {item.bulan}</Text>
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

const RekapAbsensiScreen = () => {
  const navigation = useNavigation<RekapAbsensiScreenNavigationProp>();
  const [modalVisible, setModalVisible] = useState(false);
  const [modalAction, setModalAction] = useState<'download' | 'delete' | null>(null);
  const [selectedItem, setSelectedItem] = useState<any>(null);
  const [modalStatus, setModalStatus] = useState<'confirm' | 'loading' | 'success' | 'error'>('confirm');

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
    // Reset status after a short delay to allow modal to close smoothly
    setTimeout(() => {
        setSelectedItem(null);
        setModalAction(null);
        setModalStatus('confirm');
    }, 300);
  };

  const modalStrings = useMemo(() => {
    if (!selectedItem) return { title: '', message: '', successMessage: '', errorMessage: '' };
    if (modalAction === 'download') {
      return {
        title: 'Konfirmasi Unduh',
        message: `Apakah Anda yakin ingin mengunduh rekap absensi untuk bulan ${selectedItem.bulan}?`,
        successMessage: `Rekap absensi bulan ${selectedItem.bulan} BERHASIL diunduh.`,
        errorMessage: `Rekap absensi bulan ${selectedItem.bulan} GAGAL diunduh.`,
      };
    }
    if (modalAction === 'delete') {
      return {
        title: 'Konfirmasi Hapus',
        message: `Apakah Anda yakin ingin menghapus rekap absensi untuk bulan ${selectedItem.bulan}?`,
        successMessage: `Rekap absensi bulan ${selectedItem.bulan} BERHASIL dihapus.`,
        errorMessage: `Rekap absensi bulan ${selectedItem.bulan} GAGAL dihapus.`,
      };
    }
    return { title: '', message: '', successMessage: '', errorMessage: '' };
  }, [selectedItem, modalAction]);

  return (
    <SafeAreaView style={styles.container}>
      <Header onBackPress={() => navigation.goBack()} />
      <SearchBar />
      <ScrollView contentContainerStyle={styles.scrollViewContent}>
        {RekapData.map((item, index) => (
          <RekapAbsensiItem 
            key={item.id} 
            item={item}
            isLastItem={index === RekapData.length - 1}
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
          documentType="Rekap Absensi"
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
    backgroundColor: '#4c78afff', // Warna hijau
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
  userInfoContainer: {
    paddingHorizontal: 10,
  },
  userInfoRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  userInfoLabel: {
    color: '#FFFFFF',
    fontSize: 16,
    lineHeight: 22,
    width: 50, // Lebar tetap untuk label
  },
  userInfoValue: {
    color: '#FFFFFF',
    fontSize: 16,
    lineHeight: 22,
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
    paddingTop: 10,
    paddingBottom: 80, // Space for footer
  },
  // Rekap Item
  rekapItem: {
    paddingVertical: 12,
  },
  rekapHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  rekapMonth: {
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
    marginVertical: 4,
  },
});

export default RekapAbsensiScreen;
