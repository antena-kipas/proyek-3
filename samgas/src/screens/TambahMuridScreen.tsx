import React, { useState, useMemo } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, TextInput, ScrollView, Alert } from 'react-native';
import { Avatar, Card } from 'react-native-paper';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RootStackParamList } from '../navigation/types';
import BottomNavBar from '../components/BottomNavBar';
import ConfirmationModal from '../components/ConfirmationModal';

type TambahMuridScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'TambahMurid'>;

const TambahMuridScreen = () => {
  const navigation = useNavigation<TambahMuridScreenNavigationProp>();
  const [namaMurid, setNamaMurid] = useState('');
  const [kelas, setKelas] = useState('');

  const [modalVisible, setModalVisible] = useState(false);
  const [modalStatus, setModalStatus] = useState<'confirm' | 'loading' | 'success' | 'error'>('confirm');

  const handleSimpan = () => {
    if (!namaMurid.trim() || !kelas.trim()) {
      Alert.alert('Input Tidak Lengkap', 'Nama murid dan kelas tidak boleh kosong.');
      return;
    }
    setModalStatus('confirm');
    setModalVisible(true);
  };

  const handleConfirm = () => {
    setModalStatus('loading');
    setTimeout(() => {
      const isSuccess = Math.random() < 0.5; // 50% chance of success
      if (isSuccess) {
        setModalStatus('success');
        // Reset form on success
        setNamaMurid('');
        setKelas('');
      } else {
        setModalStatus('error');
      }
    }, 2000);
  };

  const handleClose = () => {
    setModalVisible(false);
    // Reset modal to confirm state for next time after a short delay
    setTimeout(() => {
        setModalStatus('confirm');
    }, 300);
  };

  const modalStrings = useMemo(() => {
    const studentInfo = `murid "${namaMurid}" ke kelas "${kelas}"`;
    return {
        title: 'Konfirmasi Simpan',
        message: `Apakah Anda yakin ingin menyimpan data ${studentInfo}? Pastikan semua data sudah benar.`,
        successMessage: `Data ${studentInfo} BERHASIL disimpan.`,
        errorMessage: `Data ${studentInfo} GAGAL disimpan. Silakan coba lagi.`,
    };
  }, [namaMurid, kelas]);

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Card style={styles.card}>
          <Card.Content>
            <Avatar.Icon
              icon="account-plus-outline"
              size={36}
              color="#000"
              style={styles.icon}
            />
          </Card.Content>
        </Card>
        <Text style={styles.headerTitle}>Tambah Murid</Text>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Text style={styles.backButtonText}>←</Text>
        </TouchableOpacity>
      </View>

      <ScrollView style={styles.formContainer}>
        <Text style={styles.label}>Nama Murid</Text>
        <TextInput
          style={styles.input}
          placeholder="Masukkan nama lengkap murid"
          value={namaMurid}
          onChangeText={setNamaMurid}
        />

        <Text style={styles.label}>Kelas</Text>
        <TextInput
          style={styles.input}
          placeholder="Contoh: 1, 2, 3"
          value={kelas}
          onChangeText={(text) => setKelas(text.replace(/[^0-9]/g, ''))}
          keyboardType='numeric'
        />

        <TouchableOpacity style={styles.saveButton} onPress={handleSimpan}>
          <Text style={styles.saveButtonText}>Simpan</Text>
        </TouchableOpacity>
      </ScrollView>

      <BottomNavBar />

      <ConfirmationModal
        visible={modalVisible}
        status={modalStatus}
        onConfirm={modalStatus === 'confirm' ? handleConfirm : handleClose}
        onCancel={handleClose}
        onRetry={handleConfirm}
        title={modalStrings.title}
        message={modalStrings.message}
        successMessage={modalStrings.successMessage}
        errorMessage={modalStrings.errorMessage}
        documentType="Murid"
      />
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
    justifyContent: 'center',
    paddingHorizontal: 20,
    marginTop: 50,
    marginBottom: 30,
    position: 'relative',
    height: 60,
  },
  card: {
    position: 'absolute',
    left: 20,
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
    color: 'black',
    paddingLeft: 30, // Shift title to the right for better balance
  },
  backButton: {
    position: 'absolute',
    right: 20,
    height: '100%',
    justifyContent: 'center',
  },
  backButtonText: {
    fontSize: 30,
    color: '#000',
    fontWeight: 'bold',
  },
  formContainer: {
    paddingHorizontal: 20,
  },
  label: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#3F51B5',
    marginBottom: 8,
  },
  input: {
    backgroundColor: '#F0F0F0',
    borderRadius: 10,
    padding: 15,
    fontSize: 16,
    marginBottom: 20,
  },
  saveButton: {
    backgroundColor: '#3F51B5',
    padding: 18,
    borderRadius: 10,
    alignItems: 'center',
    marginTop: 10,
  },
  saveButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
});

export default TambahMuridScreen;
