import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, TextInput, ScrollView, KeyboardAvoidingView, Platform } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Button } from 'react-native-paper';
import { useNavigation } from '@react-navigation/native';
import BottomNavBar from '../components/BottomNavBar';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RootStackParamList } from '../navigation/types';
import ConfirmationModal from '../components/ConfirmationModal';

type BuatSilabusScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'BuatSilabus'>;

const LabeledInput = ({ label, value, onChangeText, placeholder }: { label: string, value: string, onChangeText: (text: string) => void, placeholder?: string }) => (
  <View style={styles.inputContainer}>
    <Text style={styles.label}>{label}</Text>
    <TextInput
      style={styles.input}
      value={value}
      onChangeText={onChangeText}
      placeholder={placeholder}
    />
  </View>
);

const BuatSilabusScreen = () => {
  const navigation = useNavigation<BuatSilabusScreenNavigationProp>();
  
  // --- State untuk Modal ---
  const [modalVisible, setModalVisible] = useState(false);
  const [modalStatus, setModalStatus] = useState<'confirm' | 'loading' | 'success' | 'error'>('confirm');
  const [timestamp, setTimestamp] = useState('');

  // --- Simulasi Role & State untuk Form ---
  const userRole: 'super-user' | 'guru' = 'super-user'; // Ganti ke 'guru' untuk melihat perbedaannya

  const [namaGuru, setNamaGuru] = useState('');
  const [semester, setSemester] = useState('');
  const [judulBuku, setJudulBuku] = useState('');
  const [subtema, setSubtema] = useState('');
  const [kompetensiInti, setKompetensiInti] = useState('');


  // --- Fungsi-fungsi Handler Modal ---
  const handleConfirmAndCreate = () => {
    setModalStatus('loading');
    setTimeout(() => {
      if (Math.random() > 0.5) { 
        const now = new Date();
        const formattedTimestamp = `${now.getDate().toString().padStart(2, '0')}/${(now.getMonth() + 1).toString().padStart(2, '0')} ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
        setTimestamp(formattedTimestamp);
        setModalStatus('success');
      } else { 
        setModalStatus('error');
      }
    }, 2000);
  };

  const handleRetry = () => {
    setModalStatus('loading');
    handleConfirmAndCreate();
  };

  const openModal = () => {
    setModalStatus('confirm');
    setModalVisible(true);
  };

  const closeModal = () => {
    setModalVisible(false);
    setTimeout(() => {
      setModalStatus('confirm');
      setTimestamp('');
    }, 300);
  };

  return (
    <SafeAreaView style={styles.container}>
      <ConfirmationModal
        visible={modalVisible}
        status={modalStatus}
        onConfirm={handleConfirmAndCreate}
        onCancel={closeModal}
        onRetry={handleRetry}
        title="Konfirmasi"
        message="Apakah Anda yakin ingin membuat dokumen ini?"
        documentType="Silabus"
        successMessage="BERHASIL Dibuat"
        errorMessage="GAGAL Dibuat. Silakan coba lagi."
        timestamp={timestamp}
      />

      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={styles.keyboardAvoidingContainer}
      >
        <ScrollView>
          <View style={styles.header}>
            <TouchableOpacity onPress={() => navigation.goBack()}>
              <Text style={styles.backButtonText}>←</Text>
            </TouchableOpacity>
            <Text style={styles.headerTitle}>Silabus</Text>
            <Button mode="contained" onPress={openModal} style={styles.buatButton}>
              Buat
            </Button>
          </View>

          <View style={styles.formContainer}>
            {userRole === 'super-user' && (
              <LabeledInput
                label="Nama Guru"
                value={namaGuru}
                onChangeText={setNamaGuru}
                placeholder="Masukkan nama guru"
              />
            )}
            <LabeledInput
              label="Semester"
              value={semester}
              onChangeText={setSemester}
              placeholder="Contoh: Ganjil"
            />
            <LabeledInput
              label="Judul Buku tematik"
              value={judulBuku}
              onChangeText={setJudulBuku}
              placeholder="Masukkan judul buku"
            />
            <LabeledInput
              label="Subtema"
              value={subtema}
              onChangeText={setSubtema}
              placeholder="Masukkan subtema"
            />
            <LabeledInput
              label="Kompetensi inti"
              value={kompetensiInti}
              onChangeText={setKompetensiInti}
              placeholder="Masukkan kompetensi inti"
            />
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
      <BottomNavBar />
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  keyboardAvoidingContainer: {
    flex: 1,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingBottom: 20,
    paddingTop: 20,
  },
  backButtonText: {
    fontSize: 30,
    color: '#000',
    fontWeight: 'bold',
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: 'black',
  },
  buatButton: {
    borderRadius: 20,
  },
  formContainer: {
    paddingHorizontal: 20,
  },
  inputContainer: {
    marginBottom: 20,
  },
  label: {
    fontSize: 16,
    color: 'black',
    marginBottom: 5,
    fontWeight: 'bold',
  },
  input: {
    borderBottomWidth: 1,
    borderBottomColor: 'lightgray',
    fontSize: 18,
    paddingVertical: 5,
  },
});

export default BuatSilabusScreen;