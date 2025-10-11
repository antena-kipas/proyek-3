import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Avatar, Card } from 'react-native-paper';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RootStackParamList } from '../navigation/types';
import BottomNavBar from '../components/BottomNavBar';

type AbsensiScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'Absensi'>;

const AbsensiScreen = () => {
  const navigation = useNavigation<AbsensiScreenNavigationProp>();
  const userRole: 'super-user' | 'guru' = 'super-user'; // Hardcoded for now

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Card style={styles.card}>
          <Card.Content>
            <Avatar.Icon
              icon="account-check-outline" // Ikon yang sesuai untuk Absensi
              size={36}
              color="#000"
              style={styles.icon}
            />
          </Card.Content>
        </Card>

        <Text style={styles.headerTitle}>Absensi</Text>

        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Text style={styles.backButtonText}>←</Text>
        </TouchableOpacity>
      </View>

      <Text style={styles.menuTitle}>Daftar Menu</Text>
      {userRole === 'super-user' ? (
        <>
          <TouchableOpacity style={styles.menuButton} onPress={() => navigation.navigate('TambahMurid')}>
            <Text style={styles.menuButtonText}>Tambah Murid</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.menuButton} onPress={() => navigation.navigate('HapusMurid')}>
            <Text style={styles.menuButtonText}>Hapus Murid</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.menuButton} onPress={() => navigation.navigate('DokumenAbsensi')}>
            <Text style={styles.menuButtonText}>Dokumen Absensi</Text>
          </TouchableOpacity>
        </>
      ) : (
        <>
          <TouchableOpacity style={styles.menuButton} onPress={() => navigation.navigate('BuatAbsensi')}>
            <Text style={styles.menuButtonText}>Buat Absensi</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.menuButton} onPress={() => navigation.navigate('RekapAbsensi')}>
            <Text style={styles.menuButtonText}>Rekap Absensi</Text>
          </TouchableOpacity>
        </>
      )}
      <BottomNavBar />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingHorizontal: 20,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 50,
    marginBottom: 30,
    position: 'relative',
    height: 60,
  },
  card: {
    position: 'absolute',
    left: 0,
    width: 60,
    height: 60,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#E0E0E0',
    borderRadius: 15,
  },
  icon: {
    backgroundColor: 'transparent',
    
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    textAlign: 'center',
    color: 'black',
  },
  backButton: {
    position: 'absolute',
    right: 0,
    height: '100%',
    justifyContent: 'center',
    paddingHorizontal: 5,
  },
  backButtonText: {
    fontSize: 30,
    color: '#000',
    fontWeight: 'bold',
  },
  menuTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#3F51B5',
    marginBottom: 20,
  },
  menuButton: {
    backgroundColor: '#E0E0E0',
    padding: 15,
    borderRadius: 10,
    marginBottom: 15,
  },
  menuButtonText: {
    fontSize: 16,
    textAlign: 'center',
    color: '#000',
    fontWeight: 'bold',
  },
});

export default AbsensiScreen;
