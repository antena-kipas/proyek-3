import React, { useContext } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, TextInput } from 'react-native';
import { IconButton } from 'react-native-paper';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RootStackParamList } from '../navigation/types';
import BottomNavBar from '../components/BottomNavBar';
import { AuthContext } from '../context/AuthContext'; // Import Context

type ProfileScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'Profile'>;

const ProfileScreen = () => {
  const navigation = useNavigation<ProfileScreenNavigationProp>();
  
  // PERBAIKAN 1: Ambil 'userInfo' dari Context
  const { logout, userRole, userInfo } = useContext(AuthContext); 

  return (
    <View style={styles.container}>
      {/* --- HEADER --- */}
      <View style={styles.header}>
        <View style={styles.headerTitleContainer}>
            <View style={styles.headerIconContainer}>
                <IconButton icon="account-circle" size={35} />
            </View>
            <Text style={styles.headerTitle}>Profile</Text>
        </View>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Text style={styles.backButtonText}>←</Text>
        </TouchableOpacity>
      </View>

      {/* --- CONTENT --- */}
      <View style={styles.content}>
        <View style={styles.profileInfo}>

            {/* PERBAIKAN 2: Tampilkan Nama Asli dari Database */}
            <View style={styles.infoBox}>
                <Text style={styles.label}>Nama Lengkap</Text>
                <TextInput
                  style={styles.input}
                  // Jika userInfo ada, tampilkan namanya. Jika loading, tulis Memuat...
                  value={userInfo ? userInfo.name : "Memuat Data..."} 
                  editable={false}
                />
            </View>

            {/* Bagian Email (Opsional, tapi bagus ditampilkan) */}
             <View style={styles.infoBox}>
                <Text style={styles.label}>Email</Text>
                <TextInput
                  style={styles.input}
                  value={userInfo ? userInfo.email : "-"} 
                  editable={false}
                />
            </View>

            {/* Form Role / Kelas */}
            <View style={styles.infoBox}>
                <Text style={styles.label}>Role / Jabatan</Text>
                <TextInput
                  style={styles.input}
                  value={userRole ? userRole.toUpperCase() : "MEMUAT..."}
                  editable={false}
                />
            </View>

            {/* Tombol Logout */}
            <TouchableOpacity 
              style={[styles.button, styles.logoutButton]}
              onPress={logout} 
            >
              <Text style={styles.buttonText}>Logout</Text>
            </TouchableOpacity>
        </View>
      </View>
      
      {/* Navigasi Bawah */}
      <BottomNavBar />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 15,
    paddingTop: 50,
    paddingBottom: 20,
    backgroundColor: '#fff',
  },
  headerTitleContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  headerIconContainer: {
    width: 50,
    height: 50,
    borderRadius: 10,
    backgroundColor: '#E0E0E0',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
  },
  backButton: {
    padding: 5,
  },
  backButtonText: {
    fontSize: 30,
    color: '#000',
    fontWeight: 'bold'
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#000',
  },
  content: {
    flex: 1,
    padding: 20,
  },
  profileInfo: {
    width: '100%',
  },
  infoBox: {
    width: '100%',
    marginBottom: 15,
  },
  label: {
    fontSize: 16,
    color: '#888',
    marginBottom: 5,
  },
  input: {
    backgroundColor: '#F5F5F5',
    borderRadius: 10,
    padding: 15,
    fontSize: 16,
    color: '#000',
  },
  button: {
    padding: 15,
    borderRadius: 10,
    alignItems: 'center',
    marginTop: 20, 
  },
  logoutButton: {
    backgroundColor: '#DC3545', // Warna Merah untuk Logout
  },
  buttonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});

export default ProfileScreen;