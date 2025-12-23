import React, { useState, useMemo, useEffect, useCallback, useContext } from 'react';
import { StyleSheet, View, TouchableOpacity, FlatList, ActivityIndicator, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useFocusEffect } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Text, Icon, TextInput, Button } from 'react-native-paper'; // Import Button untuk Coba Lagi
import { RootStackParamList } from '../navigation/types';
import BottomNavBar from '../components/BottomNavBar';
// import ConfirmationModal from '../components/ConfirmationModal'; // Tidak diperlukan lagi setelah tombol hapus/unduh dihapus
import { AuthContext } from '../context/AuthContext';
import axios from 'axios';
import { API_BASE_URL } from '../config/api';

type DaftarRPPScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'DaftarRPP'>;

// --- KOMPONEN ---

const Header = ({ onBackPress, userNama, userKelas }: { onBackPress: () => void; userNama: string; userKelas: string; }) => (
  <View style={styles.header}>
    <View style={styles.headerTop}>
      <TouchableOpacity onPress={onBackPress}>
        <Icon source="arrow-left" size={28} color="#FFFFFF" />
      </TouchableOpacity>
      <Text style={styles.headerTitle}>Daftar RPP</Text>
      <View style={{ width: 28 }} />
    </View>
    <UserInfo nama={userNama} kelas={userKelas} />
  </View>
);

const UserInfo = ({ nama, kelas }: { nama: string; kelas: string; }) => (
    <View style={styles.userInfo}>
        <View>
            <Text style={styles.userInfoText}>Nama    : </Text>
            <Text style={styles.userInfoText}>Kelas      :</Text>
        </View>
        <View>
            <Text style={styles.userInfoText}>{nama}</Text>
            <Text style={styles.userInfoText}>{kelas}</Text>
        </View>
    </View>
);

const SearchBar = () => {
  const [searchQuery, setSearchQuery] = React.useState('');
  return (
    <View style={styles.searchContainer}>
      <TextInput
        label="Cari RPP"
        value={searchQuery}
        onChangeText={setSearchQuery}
        style={styles.searchInput}
        mode="outlined"
        left={<TextInput.Icon icon="magnify" />}
      />
    </View>
  );
};

// Interface untuk struktur RPP dari backend
interface RppItemData {
  id: number;
  semester: string;
  pembelajaran_ke: string;
  tema_id: string;
  tema_name: string;
  sub_tema_id: string;
  sub_tema_name: string;
  tujuan_pembelajarans: Array<{ tujuan_pembelajaran: string }>;
  muatan_terpadus: Array<{ mata_pelajaran: string }>;
  kegiatan_intis: Array<{ kelompok: string; konten: string; urutan: number; }>;
}


const RPPItem = ({ item }: { item: RppItemData }) => (
  <View style={styles.rppItem}>
    <View style={styles.rppHeader}>
      <Text style={styles.rppSubject}>Tema: {item.tema_name} ({item.tema_id})</Text>
      <View style={styles.iconContainer}>
        {/* Tombol download dan delete dihapus sesuai permintaan */}
      </View>
    </View>
    <Text style={styles.rppDetail}>Sub Tema: {item.sub_tema_name} ({item.sub_tema_id})</Text>
    <Text style={styles.rppDetail}>Semester: {item.semester}</Text>
    <Text style={styles.rppDetail}>Pembelajaran Ke: {item.pembelajaran_ke}</Text>
    {item.tujuan_pembelajarans && item.tujuan_pembelajarans.length > 0 && (
      <Text style={styles.rppDetail}>
        Tujuan Pembelajaran: {item.tujuan_pembelajarans.map(t => t.tujuan_pembelajaran).join(', ')}
      </Text>
    )}
  </View>
);

// --- SCREEN UTAMA ---

const DaftarRPPScreen = () => {
  const navigation = useNavigation<DaftarRPPScreenNavigationProp>();
  const { userToken, userInfo } = useContext(AuthContext);

  const [rpps, setRpps] = useState<RppItemData[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchRpps = useCallback(async (refresh = false) => {
    if (refresh) {
      setIsRefreshing(true);
    } else {
      setIsLoading(true);
    }
    setError(null);
    try {
      const response = await axios.get(`${API_BASE_URL}/rpps`, {
        headers: {
          Authorization: `Bearer ${userToken}`,
        },
      });
      setRpps(response.data);
    } catch (err: any) {
      console.error('Failed to fetch RPPs:', err);
      setError('Gagal memuat daftar RPP. Coba lagi nanti.');
      if (err.response) {
        console.error('Error response:', err.response.data);
      }
    } finally {
      setIsLoading(false);
      setIsRefreshing(false);
    }
  }, [userToken]);

  useFocusEffect(
    useCallback(() => {
      fetchRpps();
    }, [fetchRpps])
  );

  return (
    <SafeAreaView style={styles.container}>
      <Header onBackPress={() => navigation.goBack()} userNama={userInfo?.name ?? ''} userKelas={userInfo?.kelas?.toString() ?? ''} />
      <SearchBar />

      {isLoading ? (
        <ActivityIndicator size="large" color="#0000ff" style={styles.loadingIndicator} />
      ) : error ? (
        <View style={styles.errorContainer}>
          <Text style={styles.errorText}>{error}</Text>
          <Button mode="outlined" onPress={() => fetchRpps(false)}>Coba Lagi</Button>
        </View>
      ) : (
        <FlatList
          data={rpps}
          keyExtractor={(item) => item.id.toString()}
          renderItem={({ item }) => (
            <RPPItem
              item={item}
            />
          )}
          contentContainerStyle={styles.flatListContent}
          onRefresh={() => fetchRpps(true)}
          refreshing={isRefreshing}
          ListEmptyComponent={
            <View style={styles.emptyListContainer}>
              <Text style={styles.emptyListText}>Belum ada RPP yang dibuat.</Text>
            </View>
          }
        />
      )}
      
      {/* Tombol Buat RPP Baru */}
      <TouchableOpacity
        style={styles.fab}
        onPress={() => navigation.navigate('BuatRPP')} // Navigasi ke layar Buat RPP
      >
        <Icon source="plus" size={28} color="#FFFFFF" />
      </TouchableOpacity>

      <BottomNavBar />
      {/* ConfirmationModal tidak lagi digunakan */}
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
  // FlatList content
  flatListContent: {
    paddingHorizontal: 16,
    paddingTop: 10,
    paddingBottom: 80, // Space for footer and FAB
  },
  // RPP Item
  rppItem: {
    backgroundColor: '#FFFFFF',
    borderRadius: 8,
    padding: 15,
    marginBottom: 10,
    elevation: 2, // Android shadow
    shadowColor: '#000', // iOS shadow
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.22,
    shadowRadius: 2.22,
  },
  rppHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  rppSubject: {
    fontSize: 16,
    fontWeight: '600',
    color: '#000',
  },
  iconContainer: {
    flexDirection: 'row',
  },
  rppDetail: {
    fontSize: 14,
    color: '#3C3C43',
    marginBottom: 4,
    lineHeight: 20,
  },
  // Loading & Error
  loadingIndicator: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  errorText: {
    color: 'red',
    fontSize: 16,
    textAlign: 'center',
    marginBottom: 10,
  },
  emptyListContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  emptyListText: {
    fontSize: 16,
    color: '#666',
    textAlign: 'center',
  },
  // Floating Action Button
  fab: {
    position: 'absolute',
    width: 60,
    height: 60,
    alignItems: 'center',
    justifyContent: 'center',
    right: 20,
    bottom: 90, // Above BottomNavBar
    backgroundColor: '#007AFF',
    borderRadius: 30,
    elevation: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 4.65,
  },
});

export default DaftarRPPScreen;
