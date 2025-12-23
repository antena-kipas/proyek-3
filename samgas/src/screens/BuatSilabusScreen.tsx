import React, { useState, useContext, useEffect, useCallback } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, KeyboardAvoidingView, Platform, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Button, TextInput, Icon } from 'react-native-paper';
import { useNavigation } from '@react-navigation/native';
import RNPickerSelect from 'react-native-picker-select';
import axios from 'axios';
import { API_BASE_URL } from '../config/api';

import { RootStackParamList } from '../navigation/types';
import { AuthContext } from '../context/AuthContext';
import BottomNavBar from '../components/BottomNavBar';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';

type BuatSilabusScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'BuatSilabus'>;

// --- Main Component ---
const BuatSilabusScreen = () => {
  const navigation = useNavigation<BuatSilabusScreenNavigationProp>();
  const { userInfo, userToken } = useContext(AuthContext);
  const isGuru = userInfo?.role === 'guru';

  // --- Form States ---
  const [formData, setFormData] = useState({
    kelas: isGuru ? userInfo?.kelas?.toString() ?? '' : '',
    semester: '',
    id_tema: '',
    id_subtema: '',
    tema: '',
    subtema: '',
    mata_pelajaran_id: '',
  });
  
  const [mataPelajaranOptions, setMataPelajaranOptions] = useState([]);
  const [kompetensiIntis, setKompetensiIntis] = useState([{ id: 1, kompetensi_inti: '' }]);
  const [kompetensiDasars, setKompetensiDasars] = useState([{ id: 1, deskripsi_kd: '' }]);
  const [indikators, setIndikators] = useState([{ id: 1, deskripsi_indikator: '' }]);
  const [materiPelajaran, setMateriPelajaran] = useState<any[]>([]);
  const [kegiatanPembelajaran, setKegiatanPembelajaran] = useState<any[]>([]);
  const [penilaianDiri, setPenilaianDiri] = useState<any[]>([]);
  
  // --- Loading States ---
  const [isGenerating, setIsGenerating] = useState(false);
  const [isSaving, setIsSaving] = useState(false);

  // Fetch Mata Pelajaran options on mount
  useEffect(() => {
    const fetchMataPelajaran = async () => {
      try {
        const response = await axios.get(`${API_BASE_URL}/mata-pelajaran`, {
          headers: { Authorization: `Bearer ${userToken}` },
        });
        const options = response.data.map((mapel: any) => ({
          label: mapel.nama_pelajaran,
          value: mapel.id.toString(),
        }));
        setMataPelajaranOptions(options);
      } catch (error) {
        console.error("Failed to fetch mata pelajaran:", error);
        Alert.alert('Error', 'Gagal memuat daftar mata pelajaran.');
      }
    };
    fetchMataPelajaran();
  }, [userToken]);

  // --- Handlers ---
  const handleFormChange = (name: string, value: string) => {
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const addKompetensiInti = () => setKompetensiIntis(prev => [...prev, { id: Date.now(), kompetensi_inti: '' }]);
  const removeKompetensiInti = (id: number) => setKompetensiIntis(prev => prev.filter(item => item.id !== id));
  const handleKompetensiIntiChange = (id: number, text: string) => setKompetensiIntis(prev => prev.map(item => (item.id === id ? { ...item, kompetensi_inti: text } : item)));

  const addKompetensiDasar = () => setKompetensiDasars(prev => [...prev, { id: Date.now(), deskripsi_kd: '' }]);
  const removeKompetensiDasar = (id: number) => setKompetensiDasars(prev => prev.filter(item => item.id !== id));
  const handleKompetensiDasarChange = (id: number, text: string) => setKompetensiDasars(prev => prev.map(item => (item.id === id ? { ...item, deskripsi_kd: text } : item)));

  const addIndikator = () => setIndikators(prev => [...prev, { id: Date.now(), deskripsi_indikator: '' }]);
  const removeIndikator = (id: number) => setIndikators(prev => prev.filter(item => item.id !== id));
  const handleIndikatorChange = (id: number, text: string) => setIndikators(prev => prev.map(item => (item.id === id ? { ...item, deskripsi_indikator: text } : item)));

  const addMateriPelajaran = () => setMateriPelajaran(prev => [...prev, { id: Date.now(), materi_pelajaran: '' }]);
  const removeMateriPelajaran = (id: number) => setMateriPelajaran(prev => prev.filter(item => item.id !== id));
  const handleMateriPelajaranChange = (id: number, text: string) => setMateriPelajaran(prev => prev.map(item => (item.id === id ? { ...item, materi_pelajaran: text } : item)));

  const addKegiatanPembelajaran = () => setKegiatanPembelajaran(prev => [...prev, { id: Date.now(), kegiatan_pembelajaran: '' }]);
  const removeKegiatanPembelajaran = (id: number) => setKegiatanPembelajaran(prev => prev.filter(item => item.id !== id));
  const handleKegiatanPembelajaranChange = (id: number, text: string) => setKegiatanPembelajaran(prev => prev.map(item => (item.id === id ? { ...item, kegiatan_pembelajaran: text } : item)));

  const addPenilaianDiri = () => setPenilaianDiri(prev => [...prev, { id: Date.now(), penilaian_diri: '' }]);
  const removePenilaianDiri = (id: number) => setPenilaianDiri(prev => prev.filter(item => item.id !== id));
  const handlePenilaianDiriChange = (id: number, text: string) => setPenilaianDiri(prev => prev.map(item => (item.id === id ? { ...item, penilaian_diri: text } : item)));

  const handleGenerateAi = async () => {
    const requiredFields = ['kelas', 'semester', 'tema', 'subtema', 'mata_pelajaran_id'];
    for (const field of requiredFields) {
      if (!formData[field as keyof typeof formData]) {
        Alert.alert('Data Tidak Lengkap', `Harap isi bidang "${field.replace(/_/g, ' ')}" sebelum generate AI.`);
        return;
      }
    }
    setIsGenerating(true);
    try {
      const payload = { ...formData, kompetensi_intis: kompetensiIntis.map(item => ({ kompetensi_inti: item.kompetensi_inti })), kompetensi_dasars: kompetensiDasars.map(item => ({ deskripsi_kd: item.deskripsi_kd })), indikators: indikators.map(item => ({ deskripsi_indikator: item.deskripsi_indikator })), };
      const response = await axios.post(`${API_BASE_URL}/silabus/generate-details`, payload, { headers: { Authorization: `Bearer ${userToken}` } });
      if (response.data) {
        const addId = (items: any[]) => (items || []).map(item => ({ ...item, id: Date.now() + Math.random() }));
        setMateriPelajaran(addId(response.data.materi_pelajaran));
        setKegiatanPembelajaran(addId(response.data.kegiatan_pembelajaran));
        setPenilaianDiri(addId(response.data.penilaian_diri));
        Alert.alert('Sukses', 'Detail Silabus berhasil dibuat oleh AI!');
      } else { throw new Error('Respons AI tidak valid'); }
    } catch (err: any) {
      console.error('AI Generation failed:', err);
      Alert.alert('Error', 'Gagal menghasilkan detail dari AI. Mohon coba lagi.');
    } finally {
      setIsGenerating(false);
    }
  };

  const handleBuat = async () => {
    setIsSaving(true);
    try {
        const clean = (items: any[], key: string) => items.map(item => ({ [key]: item[key] }));
        const payload = { ...formData, kompetensiIntis: clean(kompetensiIntis, 'kompetensi_inti'), kompetensiDasars: clean(kompetensiDasars, 'deskripsi_kd'), indikators: clean(indikators, 'deskripsi_indikator'), materiPelajaran: clean(materiPelajaran, 'materi_pelajaran'), kegiatanPembelajaran: clean(kegiatanPembelajaran, 'kegiatan_pembelajaran'), penilaianDiri: clean(penilaianDiri, 'penilaian_diri'), };
        await axios.post(`${API_BASE_URL}/silabus`, payload, { headers: { Authorization: `Bearer ${userToken}` } });
        Alert.alert('Sukses', 'Silabus berhasil disimpan.', [{ text: 'OK', onPress: () => navigation.goBack() }]);
    } catch (err: any) {
        console.error('Save Silabus failed:', err);
        let msg = 'Gagal menyimpan silabus.';
        if (err.response?.data?.errors) { msg = Object.values(err.response.data.errors).flat().join('\n'); }
        Alert.alert('Error', msg);
    } finally {
        setIsSaving(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={{ flex: 1 }}>
        <View style={styles.header}>
          <TouchableOpacity onPress={() => navigation.goBack()}><Text style={styles.backButtonText}>←</Text></TouchableOpacity>
          <Text style={styles.headerTitle}>Buat Silabus Baru</Text>
          <Button mode="contained" onPress={handleBuat} style={styles.buatButton} loading={isSaving} disabled={isSaving}>Buat</Button>
        </View>

        <ScrollView style={styles.formContainer} showsVerticalScrollIndicator={false}>
          {isGuru ? <TextInput label="Kelas" value={formData.kelas} style={[styles.input, styles.disabledInput]} mode="outlined" editable={false}/>
           : <View style={pickerStyles.pickerContainer}><RNPickerSelect onValueChange={value => handleFormChange('kelas', value)} items={[...Array(6)].map((_, i) => ({ label: `Kelas ${i + 1}`, value: `${i + 1}` }))} placeholder={{ label: "Pilih Kelas", value: null }} style={pickerStyles.rnPicker} value={formData.kelas}/></View>}
          <View style={pickerStyles.pickerContainer}><RNPickerSelect onValueChange={value => handleFormChange('semester', value)} items={[{ label: 'Semester 1', value: '1' }, { label: 'Semester 2', value: '2' }]} placeholder={{ label: "Pilih Semester", value: null }} style={pickerStyles.rnPicker} value={formData.semester}/></View>
          <View style={pickerStyles.pickerContainer}><RNPickerSelect onValueChange={value => handleFormChange('mata_pelajaran_id', value)} items={mataPelajaranOptions} placeholder={{ label: "Pilih Mata Pelajaran", value: null }} style={pickerStyles.rnPicker} value={formData.mata_pelajaran_id}/></View>

          <TextInput label="Tema ke-" value={formData.id_tema} onChangeText={text => handleFormChange('id_tema', text)} style={styles.input} mode="outlined" keyboardType="numeric" />
          <TextInput label="Subtema ke-" value={formData.id_subtema} onChangeText={text => handleFormChange('id_subtema', text)} style={styles.input} mode="outlined" keyboardType="numeric" />
          <TextInput label="Judul Buku Tematik" value={formData.tema} onChangeText={text => handleFormChange('tema', text)} style={styles.input} mode="outlined" />
          <TextInput label="Judul/Nama Bab Subtema" value={formData.subtema} onChangeText={text => handleFormChange('subtema', text)} style={styles.input} mode="outlined" />
          
          <View style={styles.repeaterContainer}>
            <Text style={styles.repeaterLabel}>Kompetensi Inti</Text>
            {kompetensiIntis.map((item, index) => (
              <View key={item.id} style={styles.repeaterItem}><TextInput label={`Kompetensi Inti ${index + 1}`} value={item.kompetensi_inti} onChangeText={text => handleKompetensiIntiChange(item.id, text)} style={styles.repeaterInput} mode="outlined" multiline/><TouchableOpacity onPress={() => removeKompetensiInti(item.id)}><Icon source="delete" size={24} color="red" /></TouchableOpacity></View>
            ))}
            <Button icon="plus" mode="outlined" onPress={addKompetensiInti} style={styles.addButton}>Tambah Kompetensi Inti</Button>
          </View>
          
          <View style={styles.repeaterContainer}>
            <Text style={styles.repeaterLabel}>Kompetensi Dasar</Text>
            {kompetensiDasars.map((item, index) => (
              <View key={item.id} style={styles.repeaterItem}><TextInput label={`Kompetensi Dasar ${index + 1}`} value={item.deskripsi_kd} onChangeText={text => handleKompetensiDasarChange(item.id, text)} style={styles.repeaterInput} mode="outlined" multiline/><TouchableOpacity onPress={() => removeKompetensiDasar(item.id)}><Icon source="delete" size={24} color="red" /></TouchableOpacity></View>
            ))}
            <Button icon="plus" mode="outlined" onPress={addKompetensiDasar} style={styles.addButton}>Tambah Kompetensi Dasar</Button>
          </View>

          <View style={styles.repeaterContainer}>
            <Text style={styles.repeaterLabel}>Indikator</Text>
            {indikators.map((item, index) => (
              <View key={item.id} style={styles.repeaterItem}><TextInput label={`Indikator ${index + 1}`} value={item.deskripsi_indikator} onChangeText={text => handleIndikatorChange(item.id, text)} style={styles.repeaterInput} mode="outlined" multiline/><TouchableOpacity onPress={() => removeIndikator(item.id)}><Icon source="delete" size={24} color="red" /></TouchableOpacity></View>
            ))}
            <Button icon="plus" mode="outlined" onPress={addIndikator} style={styles.addButton}>Tambah Indikator</Button>
          </View>

          <Button icon="robot-happy-outline" mode="contained" style={styles.generateButton} onPress={handleGenerateAi} loading={isGenerating} disabled={isGenerating}>{isGenerating ? 'Membuat...' : 'Generate Detail Silabus dengan AI'}</Button>

          <View style={styles.repeaterContainer}>
            <Text style={styles.repeaterLabel}>Materi Pelajaran</Text>
            {materiPelajaran.map((item, index) => (
              <View key={item.id} style={styles.repeaterItem}><TextInput label={`Materi ${index + 1}`} value={item.materi_pelajaran} onChangeText={text => handleMateriPelajaranChange(item.id, text)} style={styles.repeaterInput} mode="outlined" multiline/><TouchableOpacity onPress={() => removeMateriPelajaran(item.id)}><Icon source="delete" size={24} color="red" /></TouchableOpacity></View>
            ))}
            <Button icon="plus" mode="outlined" onPress={addMateriPelajaran} style={styles.addButton}>Tambah Materi</Button>
          </View>
          
          <View style={styles.repeaterContainer}>
            <Text style={styles.repeaterLabel}>Kegiatan Pembelajaran</Text>
            {kegiatanPembelajaran.map((item, index) => (
              <View key={item.id} style={styles.repeaterItem}><TextInput label={`Kegiatan ${index + 1}`} value={item.kegiatan_pembelajaran} onChangeText={text => handleKegiatanPembelajaranChange(item.id, text)} style={styles.repeaterInput} mode="outlined" multiline/><TouchableOpacity onPress={() => removeKegiatanPembelajaran(item.id)}><Icon source="delete" size={24} color="red" /></TouchableOpacity></View>
            ))}
            <Button icon="plus" mode="outlined" onPress={addKegiatanPembelajaran} style={styles.addButton}>Tambah Kegiatan</Button>
          </View>

          <View style={styles.repeaterContainer}>
            <Text style={styles.repeaterLabel}>Penilaian Diri</Text>
            {penilaianDiri.map((item, index) => (
              <View key={item.id} style={styles.repeaterItem}><TextInput label={`Penilaian ${index + 1}`} value={item.penilaian_diri} onChangeText={text => handlePenilaianDiriChange(item.id, text)} style={styles.repeaterInput} mode="outlined" multiline/><TouchableOpacity onPress={() => removePenilaianDiri(item.id)}><Icon source="delete" size={24} color="red" /></TouchableOpacity></View>
            ))}
            <Button icon="plus" mode="outlined" onPress={addPenilaianDiri} style={styles.addButton}>Tambah Penilaian</Button>
          </View>

          <View style={{ height: 120 }} />
        </ScrollView>
      </KeyboardAvoidingView>
      <BottomNavBar />
    </SafeAreaView>
  );
};

// --- STYLES ---
const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff' },
  header: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 20, paddingTop: 20, paddingBottom: 10 },
  backButtonText: { fontSize: 30, color: '#000', fontWeight: 'bold' },
  headerTitle: { fontSize: 20, fontWeight: 'bold', color: 'black' },
  buatButton: { borderRadius: 20 },
  formContainer: { paddingHorizontal: 20 },
  input: { marginBottom: 10 },
  disabledInput: { backgroundColor: '#f0f0f0' },
  repeaterContainer: { marginTop: 20, padding: 10, backgroundColor: '#f7f7f7', borderColor: '#e0e0e0', borderWidth: 1, borderRadius: 8 },
  repeaterLabel: { fontSize: 16, fontWeight: 'bold', marginBottom: 10, color: '#333' },
  repeaterItem: { flexDirection: 'row', alignItems: 'center', marginBottom: 5, gap: 8 },
  repeaterInput: { flex: 1 },
  addButton: { marginTop: 10 },
  generateButton: { marginVertical: 20 },
});

const pickerStyles = {
  pickerContainer: { marginBottom: 10, borderColor: '#888', borderWidth: 1, borderRadius: 4, backgroundColor: 'transparent', justifyContent: 'center' },
  rnPicker: StyleSheet.create({
    inputIOS: { fontSize: 16, paddingVertical: 16, paddingHorizontal: 10, color: 'black', paddingRight: 30 },
    inputAndroid: { fontSize: 16, paddingHorizontal: 10, paddingVertical: 15, color: 'black', paddingRight: 30 },
  }),
};

export default BuatSilabusScreen;