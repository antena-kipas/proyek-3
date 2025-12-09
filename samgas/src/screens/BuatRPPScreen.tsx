import React, { useState, useContext } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Platform, KeyboardAvoidingView, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Button, TextInput, IconButton } from 'react-native-paper';
import { useNavigation } from '@react-navigation/native';
import BottomNavBar from '../components/BottomNavBar';
import { RootStackParamList } from '../navigation/types';
import ConfirmationModal from '../components/ConfirmationModal';
import { AuthContext } from '../context/AuthContext';
import RNPickerSelect from 'react-native-picker-select';
import axios from 'axios';

type BuatRPPScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'BuatRPP'>;

const BuatRPPScreen = () => {
  const navigation = useNavigation<BuatRPPScreenNavigationProp>();
  const { userInfo, userToken } = useContext(AuthContext);

  const isGuru = userInfo?.role === 'guru';

  // --- State untuk Modal ---
  const [modalVisible, setModalVisible] = useState(false);
  const [isGenerating, setIsGenerating] = useState(false); // <-- State loading generate
  const [isCreating, setIsCreating] = useState(false); // <-- State loading create

  // --- State untuk Form ---
  const [formData, setFormData] = useState({
    kelas: isGuru ? userInfo?.kelas?.toString() ?? '' : '',
    semester: '',
    pembelajaran_ke: '',
    tema_id: '',
    tema_name: '',
    sub_tema_id: '',
    sub_tema_name: '',
  });

  const [tujuanPembelajarans, setTujuanPembelajarans] = useState([{ id: 1, tujuan_pembelajaran: '' }]);
  const [muatanTerpadus, setMuatanTerpadus] = useState([{ id: 1, mata_pelajaran: '' }]);
  const [kegiatanIntis, setKegiatanIntis] = useState<any[]>([]); // <-- State baru untuk hasil AI

  const handleFormChange = (name: string, value: string) => {
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  // --- Handler untuk Generate AI ---
  const handleGenerateKegiatan = async () => {
    const { kelas, tema_name, sub_tema_name } = formData;
    const tujuanNonEmpty = tujuanPembelajarans.some(t => t.tujuan_pembelajaran.trim() !== '');

    if (!kelas || !tema_name || !sub_tema_name || !tujuanNonEmpty) {
      Alert.alert(
        'Data Tidak Lengkap',
        'Pastikan Kelas, Nama Tema, Nama Sub Tema, dan minimal satu Tujuan Pembelajaran sudah diisi.'
      );
      return;
    }

    const tujuan_string = tujuanPembelajarans
      .map((item, index) => `${index + 1}. ${item.tujuan_pembelajaran}`)
      .join('\n');

    setIsGenerating(true);
    try {
      const response = await axios.post(
        'http://localhost:8000/api/rpp/generate-kegiatan-inti',
        {
          kelas,
          tema_name,
          sub_tema_name,
          tujuan_pembelajaran_string: tujuan_string,
        },
        {
          headers: {
            Authorization: `Bearer ${userToken}`,
          },
        }
      );

      if (response.data && response.data.kegiatan_intis) {
        // Tambahkan ID unik ke setiap item kegiatan inti
        const kegiatanIntisWithIds = response.data.kegiatan_intis.map((item: any) => ({
          ...item,
          id: Date.now() + Math.random(), // ID unik
        }));
        setKegiatanIntis(kegiatanIntisWithIds);
        Alert.alert('Sukses', 'Kegiatan Inti berhasil dibuat oleh AI!');
      } else {
        throw new Error('Format respons tidak valid');
      }
    } catch (error) {
      console.error('Error generating kegiatan inti:', error);
      Alert.alert('Error', 'Gagal menghasilkan Kegiatan Inti. Silakan coba lagi.');
    } finally {
      setIsGenerating(false);
    }
  };
  
  // --- Handlers untuk Tujuan Pembelajaran ---
  const addTujuan = () => {
    setTujuanPembelajarans(prev => [...prev, { id: Date.now(), tujuan_pembelajaran: '' }]);
  };
  const removeTujuan = (id: number) => {
    setTujuanPembelajarans(prev => prev.filter(item => item.id !== id));
  };
  const handleTujuanChange = (id: number, text: string) => {
    setTujuanPembelajarans(prev =>
      prev.map(item => (item.id === id ? { ...item, tujuan_pembelajaran: text } : item))
    );
  };

  // --- Handlers untuk Muatan Terpadu ---
  const addMuatan = () => {
    setMuatanTerpadus(prev => [...prev, { id: Date.now(), mata_pelajaran: '' }]);
  };
  const removeMuatan = (id: number) => {
    setMuatanTerpadus(prev => prev.filter(item => item.id !== id));
  };
  const handleMuatanChange = (id: number, text: string) => {
    setMuatanTerpadus(prev =>
      prev.map(item => (item.id === id ? { ...item, mata_pelajaran: text } : item))
    );
  };

  // --- Fungsi-fungsi Handler Modal ---
  const handleConfirmAndCreate = async () => {
    setIsCreating(true);
    try {
      // 1. Membersihkan data dari ID sisi klien sebelum mengirim
      const cleanTujuan = tujuanPembelajarans.map(({ tujuan_pembelajaran }) => ({ tujuan_pembelajaran }));
      const cleanMuatan = muatanTerpadus.map(({ mata_pelajaran }) => ({ mata_pelajaran }));

      // 2. Membentuk payload
      const payload = {
        ...formData,
        tujuanPembelajarans: cleanTujuan,
        muatanTerpadus: cleanMuatan,
        kegiatanIntis: kegiatanIntis,
      };

      await axios.post('http://localhost:8000/api/rpps', payload, {
        headers: {
          Authorization: `Bearer ${userToken}`,
        },
      });

      // 3. Handle Sukses
      setIsCreating(false);
      closeModal();
      Alert.alert('Sukses', 'RPP berhasil disimpan ke database.', [
        { text: 'OK', onPress: () => navigation.goBack() },
      ]);

    } catch (error: any) {
      // 4. Handle Error
      setIsCreating(false);
      
      let errorMessage = 'Gagal menyimpan RPP. Silakan coba lagi.';
      if (error.response && error.response.data && error.response.data.errors) {
        // Jika ada error validasi dari backend
        const errors = error.response.data.errors;
        const errorMessages = Object.values(errors).flat();
        errorMessage = `Terjadi kesalahan validasi:\n- ${errorMessages.join('\n- ')}`;
      } else if (error.message) {
        errorMessage = error.message;
      }
      
      Alert.alert('Error', errorMessage);
    }
  };
  
  const openModal = () => {
     // Validasi sederhana sebelum membuka modal
    const { semester, pembelajaran_ke, tema_id, tema_name, sub_tema_id, sub_tema_name } = formData;
    const tujuanNonEmpty = tujuanPembelajarans.some(t => t.tujuan_pembelajaran.trim() !== '');
    const muatanNonEmpty = muatanTerpadus.some(m => m.mata_pelajaran.trim() !== '');

    if (!semester || !pembelajaran_ke || !tema_id || !tema_name || !sub_tema_id || !sub_tema_name || !tujuanNonEmpty || !muatanNonEmpty) {
      Alert.alert('Data Belum Lengkap', 'Harap isi semua field yang wajib diisi sebelum melanjutkan.');
      return;
    }
    setModalVisible(true);
  };

  const closeModal = () => setModalVisible(false);


  return (
    <SafeAreaView style={styles.container}>
      <ConfirmationModal
        visible={modalVisible}
        status={isCreating ? 'loading' : 'confirm'}
        onConfirm={handleConfirmAndCreate}
        onCancel={closeModal}
        title="Konfirmasi Pembuatan RPP"
        message="Apakah Anda yakin data yang dimasukkan sudah benar dan ingin membuat RPP ini?"
      />

      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={styles.keyboardAvoidingContainer}
      >
        <View style={styles.header}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Text style={styles.backButtonText}>←</Text>
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Buat RPP Baru</Text>
          <Button mode="contained" onPress={openModal} style={styles.buatButton}>
            Buat
          </Button>
        </View>

        <ScrollView style={styles.formContainer} showsVerticalScrollIndicator={false}>
          {isGuru ? (
            <TextInput 
              label="Kelas" 
              value={formData.kelas} 
              style={[styles.input, styles.disabledInput]} 
              mode="outlined" 
              editable={false}
            />
          ) : (
            <View style={styles.pickerContainer}>
              <RNPickerSelect
                onValueChange={(value) => handleFormChange('kelas', value)}
                items={[
                  { label: 'Kelas 1', value: '1' },
                  { label: 'Kelas 2', value: '2' },
                  { label: 'Kelas 3', value: '3' },
                  { label: 'Kelas 4', value: '4' },
                  { label: 'Kelas 5', value: '5' },
                  { label: 'Kelas 6', value: '6' },
                ]}
                placeholder={{ label: "Pilih Kelas", value: null }}
                style={pickerSelectStyles}
                value={formData.kelas}
              />
            </View>
          )}

          <View style={styles.pickerContainer}>
            <RNPickerSelect
              onValueChange={(value) => handleFormChange('semester', value)}
              items={[
                { label: 'Semester 1', value: '1' },
                { label: 'Semester 2', value: '2' },
              ]}
              placeholder={{ label: "Pilih Semester", value: null }}
              style={pickerSelectStyles}
            />
          </View>
          <TextInput label="Pembelajaran Ke" value={formData.pembelajaran_ke} onChangeText={text => handleFormChange('pembelajaran_ke', text)} style={styles.input} mode="outlined" keyboardType="numeric" />
          <TextInput label="Tema Ke" value={formData.tema_id} onChangeText={text => handleFormChange('tema_id', text)} style={styles.input} mode="outlined" keyboardType="numeric" />
          <TextInput label="Nama Buku Tematik" value={formData.tema_name} onChangeText={text => handleFormChange('tema_name', text)} style={styles.input} mode="outlined" />
          <TextInput label="Sub Tema Ke" value={formData.sub_tema_id} onChangeText={text => handleFormChange('sub_tema_id', text)} style={styles.input} mode="outlined" keyboardType="numeric" />
          <TextInput label="Nama Sub Tema" value={formData.sub_tema_name} onChangeText={text => handleFormChange('sub_tema_name', text)} style={styles.input} mode="outlined" />
        
          {/* Repeater Tujuan Pembelajaran */}
          <View style={styles.repeaterContainer}>
            <Text style={styles.repeaterLabel}>Tujuan Pembelajaran</Text>
            {tujuanPembelajarans.map((item, index) => (
              <View key={item.id} style={styles.repeaterItem}>
                <TextInput
                  label={`Tujuan ${index + 1}`}
                  value={item.tujuan_pembelajaran}
                  onChangeText={text => handleTujuanChange(item.id, text)}
                  style={styles.repeaterInput}
                  mode="outlined"
                  multiline
                />
                <IconButton icon="delete" iconColor="red" onPress={() => removeTujuan(item.id)} />
              </View>
            ))}
            <Button icon="plus" mode="outlined" onPress={addTujuan} style={styles.addButton}>
              Tambah Tujuan
            </Button>
          </View>

          {/* Repeater Muatan Terpadu */}
          <View style={styles.repeaterContainer}>
            <Text style={styles.repeaterLabel}>Muatan Terpadu</Text>
            {muatanTerpadus.map((item, index) => (
              <View key={item.id} style={styles.repeaterItem}>
                <TextInput
                  label={`Mata Pelajaran ${index + 1}`}
                  value={item.mata_pelajaran}
                  onChangeText={text => handleMuatanChange(item.id, text)}
                  style={styles.repeaterInput}
                  mode="outlined"
                />
                <IconButton icon="delete" iconColor="red" onPress={() => removeMuatan(item.id)} />
              </View>
            ))}
            <Button icon="plus" mode="outlined" onPress={addMuatan} style={styles.addButton}>
              Tambah Muatan
            </Button>
          </View>

          {/* --- Tombol Generate AI --- */}
          <Button 
            icon="robot" 
            mode="contained" 
            onPress={handleGenerateKegiatan} 
            style={styles.generateButton}
            labelStyle={{ color: 'white' }}
            disabled={isGenerating}
            loading={isGenerating}
          >
            {isGenerating ? 'Sedang Membuat...' : 'Generate Kegiatan Inti dengan AI'}
          </Button>

          {/* --- Repeater Kegiatan Inti (Hasil AI) --- */}
          {kegiatanIntis.length > 0 && (
            <View style={styles.repeaterContainer}>
              <Text style={styles.repeaterLabel}>Kegiatan Inti (Hasil AI)</Text>
              {kegiatanIntis.map((item, index) => (
                <View key={item.id} style={styles.repeaterItem}>
                  <View style={{ flex: 1 }}>
                     <View style={[styles.pickerContainer, { marginBottom: 5, backgroundColor: '#f0f0f0' }]}>
                        <RNPickerSelect
                          value={item.kelompok}
                          onValueChange={(value) => { /* TODO: Handle change */ }}
                          items={[
                                { label: 'Ayo Mengamati', value: 'ayo_mengamati' },
                                { label: 'Ayo Berdiskusi', value: 'ayo_berdiskusi' },
                                { label: 'Ayo Membaca', value: 'ayo_membaca' },
                                { label: 'Ayo Berlatih', value: 'ayo_berlatih' },
                                { label: 'Ayo Renungkan', value: 'ayo_renungkan' },
                          ]}
                          placeholder={{}}
                          style={pickerSelectStyles}
                          disabled={true} // Hasil AI tidak untuk diubah
                        />
                      </View>
                    <TextInput
                      label={`Konten Kegiatan ${index + 1}`}
                      value={item.konten}
                      onChangeText={text => { /* TODO: Handle change */ }}
                      style={[styles.repeaterInput, styles.disabledInput]}
                      mode="outlined"
                      multiline
                      editable={false} // Hasil AI tidak untuk diubah
                    />
                  </View>
                  {/* <IconButton icon="delete" iconColor="red" onPress={() => {}} /> */}
                </View>
              ))}
            </View>
          )}

          <View style={{ height: 120 }} />
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
    paddingTop: 20,
    paddingBottom: 20,
  },
  backButtonText: {
    fontSize: 30,
    color: '#000',
    fontWeight: 'bold',
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: 'black',
  },
  buatButton: {
    borderRadius: 20,
  },
  formContainer: {
    paddingHorizontal: 20,
  },
  input: {
    marginBottom: 10,
  },
  disabledInput: {
    backgroundColor: '#f0f0f0',
  },
  repeaterContainer: {
    marginTop: 20,
    padding: 10,
    backgroundColor: '#f7f7f7',
    borderColor: '#e0e0e0',
    borderWidth: 1,
    borderRadius: 8,
  },
  repeaterLabel: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 10,
    color: '#333'
  },
  repeaterItem: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 5,
  },
  repeaterInput: {
    flex: 1,
  },
  addButton: {
    marginTop: 10,
  },
  generateButton: {
    marginVertical: 20,
    backgroundColor: '#6200ee', // Warna ungu khas Material Design
  },
  pickerContainer: {
    marginBottom: 10,
    borderColor: '#888',
    borderWidth: 1,
    borderRadius: 4,
    backgroundColor: 'transparent',
  },
});

const pickerSelectStyles = StyleSheet.create({
  inputIOS: {
    fontSize: 16,
    paddingVertical: 12,
    paddingHorizontal: 10,
    color: 'black',
    paddingRight: 30,
  },
  inputAndroid: {
    fontSize: 16,
    paddingHorizontal: 10,
    paddingVertical: 8,
    color: 'black',
    paddingRight: 30,
  },
});

export default BuatRPPScreen;
