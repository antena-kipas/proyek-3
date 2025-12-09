import React, { useState, useEffect, useCallback, useContext } from 'react';
import { StyleSheet, View, TouchableOpacity, FlatList, ActivityIndicator, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useFocusEffect } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Text, Icon, TextInput, Button } from 'react-native-paper';
import { RootStackParamList } from '../navigation/types';
import BottomNavBar from '../components/BottomNavBar';
import { AuthContext } from '../context/AuthContext';
import axios from 'axios';

type DaftarSilabusScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'DaftarSilabus'>;

// --- KOMPONEN ---

const Header = ({ onBackPress, userNama, userKelas }: { onBackPress: () => void; userNama: string; userKelas: string; }) => (
  <View style={styles.header}>
    <View style={styles.headerTop}>
      <TouchableOpacity onPress={onBackPress}>
        <Icon source="arrow-left" size={28} color="#FFFFFF" />
      </TouchableOpacity>
      <Text style={styles.headerTitle}>Daftar Silabus</Text>
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

// Interface untuk struktur Silabus dari backend
interface SilabusItemData {
  id: number;
  tema: string;
  subtema: string;
  semester: string;
  kelas: string;
  mata_pelajaran: {
    nama_pelajaran: string;
  } | null;
}

const SilabusItem = ({ item }: { item: SilabusItemData }) => (
  <View style={styles.silabusItem}>
    <Text style={styles.silabusSubject}>{item.mata_pelajaran?.nama_pelajaran ?? 'Tanpa Mapel'}</Text>
    <Text style={styles.silabusDetail}>Tema: {item.tema}</Text>
    <Text style={styles.silabusDetail}>Subtema: {item.subtema}</Text>
    <Text style={styles.silabusDetail}>Kelas: {item.kelas} / Semester: {item.semester}</Text>
  </View>
);

// --- SCREEN UTAMA ---

const DaftarSilabusScreen = () => {
  const navigation = useNavigation<DaftarSilabusScreenNavigationProp>();
  const { userToken, userInfo } = useContext(AuthContext);

  const [silabusList, setSilabusList] = useState<SilabusItemData[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchSilabus = useCallback(async (refresh = false) => {
    if (refresh) {
      setIsRefreshing(true);
    } else {
      setIsLoading(true);
    }
    setError(null);
    try {
      const response = await axios.get('http://localhost:8000/api/silabus', {
        headers: {
          Authorization: `Bearer ${userToken}`,
        },
      });
      setSilabusList(response.data);
    } catch (err: any) {
      console.error('Failed to fetch silabus:', err);
      setError('Gagal memuat daftar silabus. Coba lagi nanti.');
    } finally {
      setIsLoading(false);
      setIsRefreshing(false);
    }
  }, [userToken]);

  useFocusEffect(
    useCallback(() => {
      fetchSilabus();
    }, [fetchSilabus])
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
          <Button mode="outlined" onPress={() => fetchSilabus(false)}>Coba Lagi</Button>
        </View>
      ) : (
        <FlatList
          data={silabusList}
          keyExtractor={(item) => item.id.toString()}
          renderItem={({ item }) => <SilabusItem item={item} />}
          contentContainerStyle={styles.flatListContent}
          onRefresh={() => fetchSilabus(true)}
          refreshing={isRefreshing}
          ListEmptyComponent={
            <View style={styles.emptyListContainer}>
              <Text style={styles.emptyListText}>Belum ada silabus yang dibuat.</Text>
            </View>
          }
        />
      )}
      
      <TouchableOpacity
        style={styles.fab}
        onPress={() => navigation.navigate('BuatSilabus')}
      >
        <Icon source="plus" size={28} color="#FFFFFF" />
      </TouchableOpacity>

      <BottomNavBar />
    </SafeAreaView>
  );
};

// --- STYLES ---

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#F2F2F7',
    },
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
    userInfo: {
        flexDirection: 'row',
        justifyContent: 'space-between',
    },
    userInfoText: {
        color: '#FFFFFF',
        fontSize: 16,
        lineHeight: 22,
    },
    searchContainer: {
        paddingHorizontal: 16,
        paddingTop: 16,
    },
    searchInput: {
        backgroundColor: '#FFFFFF',
    },
    flatListContent: {
        paddingHorizontal: 16,
        paddingTop: 10,
        paddingBottom: 80,
    },
    silabusItem: {
        backgroundColor: '#FFFFFF',
        borderRadius: 8,
        padding: 15,
        marginBottom: 10,
        elevation: 2,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 1 },
        shadowOpacity: 0.22,
        shadowRadius: 2.22,
    },
    silabusSubject: {
        fontSize: 16,
        fontWeight: '600',
        color: '#000',
        marginBottom: 8,
    },
    silabusDetail: {
        fontSize: 14,
        color: '#3C3C43',
        marginBottom: 4,
        lineHeight: 20,
    },
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
        marginTop: 50,
    },
    emptyListText: {
        fontSize: 16,
        color: '#666',
        textAlign: 'center',
    },
    fab: {
        position: 'absolute',
        width: 60,
        height: 60,
        alignItems: 'center',
        justifyContent: 'center',
        right: 20,
        bottom: 90,
        backgroundColor: '#007AFF',
        borderRadius: 30,
        elevation: 8,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 4.65,
    },
});

export default DaftarSilabusScreen;