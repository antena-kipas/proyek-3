import React, { useState, useMemo } from 'react';
import { StyleSheet, View, ScrollView, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Text, Icon, TextInput } from 'react-native-paper';
import { RootStackParamList } from '../navigation/types';
import BottomNavBar from '../components/BottomNavBar';
import ConfirmationModal from '../components/ConfirmationModal';

type DaftarSilabusScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'DaftarSilabus'>;

// --- DUMMY DATA ---
const silabusData = [
  {
    id: 1,
    judul_buku_tematik: 'Aku Anak Hebat',
    subtema: 'mandiri',
    semester: 2
  },
  {
    id: 2,
    judul_buku_tematik: 'Aku Anak Hebat',
    subtema: 'mandiri',
    semester: 2
  },
];

const user = {
    nama: 'JAMAL',
    kelas: 'XXII'
};

// --- KOMPONEN ---

const Header = ({ onBackPress }: { onBackPress: () => void }) => (
  <View style={styles.header}>
    <View style={styles.headerTop}>
      <TouchableOpacity onPress={onBackPress}>
        <Icon source="arrow-left" size={28} color="#FFFFFF" />
      </TouchableOpacity>
      <Text style={styles.headerTitle}>Daftar silabus</Text>
      <View style={{ width: 28 }} />
    </View>
    <UserInfo />
  </View>
);

const UserInfo = () => (
    <View style={styles.userInfo}>
        <View>
            <Text style={styles.userInfoText}>Nama    : </Text>
            <Text style={styles.userInfoText}>Kelas   :</Text>
        </View>
        <View>
            <Text style={styles.userInfoText}>{user.nama}</Text>
            <Text style={styles.userInfoText}>{user.kelas}</Text>
        </View>
    </View>
);

const SearchBar = () => {
  const [searchQuery, setSearchQuery] = React.useState('');
  return (
    <View style={styles.searchContainer}>
      <TextInput
        label="Cari Silabus"
        value={searchQuery}
        onChangeText={setSearchQuery}
        style={styles.searchInput}
        mode="outlined"
        left={<TextInput.Icon icon="magnify" />}
      />
    </View>
  );
};

const SilabusItem = ({
  item,
  isLastItem,
  onDownload,
  onDelete
}: {
  item: typeof silabusData[0],
  isLastItem: boolean,
  onDownload: (item: any) => void,
  onDelete: (item: any) => void
}) => (
  <View>
    <View style={styles.silabusItem}>
      <View style={styles.silabusHeader}>
        <View style={styles.iconContainer}>
          <TouchableOpacity onPress={() => onDownload(item)}>
            <Icon source="download-outline" size={24} color="#007AFF" />
          </TouchableOpacity>
          <TouchableOpacity style={{ marginLeft: 16 }} onPress={() => onDelete(item)}>
            <Icon source="trash-can-outline" size={24} color="#FF3B30" />
          </TouchableOpacity>
        </View>
      </View>
      <Text style={styles.silabusDetail}>Judul Buku: {item.judul_buku_tematik}</Text>
      <Text style={styles.silabusDetail}>Subtema: {item.subtema}</Text>
      <Text style={styles.silabusDetail}>Semester: {item.semester}</Text>
    </View>
    {!isLastItem && <View style={styles.separator} />}
  </View>
);

// --- SCREEN UTAMA ---

const DaftarSilabusScreen = () => {
  const navigation = useNavigation<DaftarSilabusScreenNavigationProp>();
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
    setTimeout(() => {
        setSelectedItem(null);
        setModalAction(null);
        setModalStatus('confirm');
    }, 300);
  };

  const modalStrings = useMemo(() => {
    if (!selectedItem) return { title: '', message: '', successMessage: '', errorMessage: '' };
    const docName = `silabus "${selectedItem.judul_buku_tematik} - ${selectedItem.subtema}"`;
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
      <Header onBackPress={() => navigation.goBack()} />
      <SearchBar />
      <ScrollView contentContainerStyle={styles.scrollViewContent}>
        {silabusData.map((item, index) => (
          <SilabusItem
            key={item.id}
            item={item}
            isLastItem={index === silabusData.length - 1}
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
          documentType="Silabus"
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
  userInfoText: {
    color: '#FFFFFF',
    fontSize: 16,
    lineHeight: 22,
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
  // silabus Item
  silabusItem: {
    paddingVertical: 12,
  },
  silabusHeader: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    alignItems: 'center',
    marginBottom: 8,
  },
  silabusSubject: {
    fontSize: 16,
    fontWeight: '600',
    color: '#000',
  },
  iconContainer: {
    flexDirection: 'row',
  },
  silabusDetail: {
    fontSize: 14,
    color: '#3C3C43',
    marginBottom: 4,
    lineHeight: 20,
  },
  separator: {
    height: 1,
    backgroundColor: '#D1D1D6',
  },
});

export default DaftarSilabusScreen;
