import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, TextInput, ScrollView, KeyboardAvoidingView, Platform } from 'react-native';
import { Button } from 'react-native-paper';
import { useNavigation } from '@react-navigation/native';
import BottomNavBar from '../components/BottomNavBar';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RootStackParamList } from '../navigation/types';
import ConfirmationModal from '../components/ConfirmationModal';

type BuatRPPScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'BuatRPP'>;

const LabeledInput = ({ label }: { label: string }) => (
  <View style={styles.inputContainer}>
    <Text style={styles.label}>{label}</Text>
    <TextInput style={styles.input} />
  </View>
);

const BuatRPPScreen = () => {
  const navigation = useNavigation<BuatRPPScreenNavigationProp>();
  const [modalVisible, setModalVisible] = useState(false);
  const [modalStatus, setModalStatus] = useState<'confirm' | 'loading' | 'success' | 'error'>('confirm');
  const [timestamp, setTimestamp] = useState('');

  const handleConfirmAndCreate = () => {
    setModalStatus('loading');
    // Simulate an async operation that can fail
    setTimeout(() => {
      if (Math.random() > 0.5) { // Simulate success 50% of the time
        const now = new Date();
        const formattedTimestamp = `${now.getDate().toString().padStart(2, '0')}/${(now.getMonth() + 1).toString().padStart(2, '0')} ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
        setTimestamp(formattedTimestamp);
        setModalStatus('success');
      } else { // Simulate failure
        setModalStatus('error');
      }
    }, 2000);
  };

  const handleRetry = () => {
    // Reset status to loading and try again
    setModalStatus('loading');
    handleConfirmAndCreate();
  };

  const openModal = () => {
    setModalStatus('confirm'); // Ensure it always starts with confirmation
    setModalVisible(true);
  };

  const closeModal = () => {
    setModalVisible(false);
    // Reset states after a short delay to allow animation to finish
    setTimeout(() => {
      setModalStatus('confirm');
      setTimestamp('');
    }, 300);
  };

  return (
    <View style={styles.container}>
      <ConfirmationModal
        visible={modalVisible}
        status={modalStatus}
        onConfirm={handleConfirmAndCreate}
        onCancel={closeModal}
        onRetry={handleRetry}
        title="Konfirmasi"
        message="Apakah Anda yakin ingin membuat dokumen ini?"
        documentType="RPP"
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
            <Text style={styles.headerTitle}>RPP</Text>
            <Button mode="contained" onPress={openModal} style={styles.buatButton}>
              Buat
            </Button>
          </View>

          <View style={styles.formContainer}>
            <LabeledInput label="Mata Pelajaran" />
            <LabeledInput label="Topik Materi" />
            <LabeledInput label="Alokasi Waktu" />
            <LabeledInput label="Tujuan 1" />
            <LabeledInput label="Tujuan 2" />
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
      <BottomNavBar />
    </View>
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
    paddingTop: 50,
    paddingBottom: 20,
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

export default BuatRPPScreen;
