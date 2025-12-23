import React, { useState, useEffect, useCallback, useContext } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator, Alert, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Button } from 'react-native-paper';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import DateTimePicker, { DateTimePickerEvent } from '@react-native-community/datetimepicker';
import RNPickerSelect from 'react-native-picker-select';
import axios from 'axios';
import { Icon } from 'react-native-paper'; // Import Icon
import { API_BASE_URL } from '../config/api';

import { RootStackParamList } from '../navigation/types';
import { AuthContext } from '../context/AuthContext';
import BottomNavBar from '../components/BottomNavBar';

type AbsensiScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'Absensi'>;

// --- Helper Functions ---
const formatDateForAPI = (date: Date): string => {
  const year = date.getFullYear();
  const month = (date.getMonth() + 1).toString().padStart(2, '0');
  const day = date.getDate().toString().padStart(2, '0');
  return `${year}-${month}-${day}`;
};

// --- Main Component ---
const AbsensiScreen = () => {
  const navigation = useNavigation<AbsensiScreenNavigationProp>();
  const { userToken, userInfo } = useContext(AuthContext);

  const [selectedDate, setSelectedDate] = useState(new Date());
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [selectedClass, setSelectedClass] = useState<string | null>(userInfo?.role === 'guru' ? userInfo?.kelas : null);
  
  const [attendanceData, setAttendanceData] = useState<any[]>([]);
  const [statuses, setStatuses] = useState<{ [key: string]: string }>({});
  
  const [isLoading, setIsLoading] = useState(false);
  const [isSaving, setIsSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const isSuperUser = userInfo?.role === 'super-user';

  const fetchAttendance = useCallback(async () => {
    if (!selectedClass || !selectedDate) return;

    setIsLoading(true);
    setError(null);
    setAttendanceData([]);
    setStatuses({});

    try {
      const response = await axios.get(`${API_BASE_URL}/absensi`, {
        headers: { Authorization: `Bearer ${userToken}` },
        params: {
          tanggal: formatDateForAPI(selectedDate),
          kelas_id: selectedClass,
        },
      });
      
      setAttendanceData(response.data);
      // Inisialisasi state statuses dari data yang di-fetch
      const initialStatuses = response.data.reduce((acc: any, student: any) => {
        acc[student.id] = student.status;
        return acc;
      }, {});
      setStatuses(initialStatuses);

    } catch (err: any) {
      console.error('Failed to fetch attendance:', err);
      setError('Gagal memuat data absensi. Pastikan server berjalan dan terhubung.');
    } finally {
      setIsLoading(false);
    }
  }, [userToken, selectedDate, selectedClass]);

  useEffect(() => {
    fetchAttendance();
  }, [fetchAttendance]);

  const handleSave = async () => {
    setIsSaving(true);
    try {
      const payload = {
        tanggal: formatDateForAPI(selectedDate),
        kelas_id: selectedClass,
        statuses: Object.entries(statuses).map(([studentId, status]) => ({
          id: studentId,
          status,
        })),
      };

      await axios.post(`${API_BASE_URL}/absensi/simpan`, payload, {
        headers: { Authorization: `Bearer ${userToken}` },
      });

      Alert.alert('Sukses', 'Absensi berhasil disimpan.');
      fetchAttendance(); // Refresh data setelah menyimpan

    } catch (err: any) {
      console.error('Failed to save attendance:', err);
      Alert.alert('Error', 'Gagal menyimpan absensi.');
    }
    finally {
      setIsSaving(false);
    }
  };

  const onDateChange = (event: DateTimePickerEvent, date?: Date) => {
    setShowDatePicker(false);
    if (date) {
      setSelectedDate(date);
    }
  };

  const handleStatusChange = (studentId: string, status: string) => {
    setStatuses(prev => ({ ...prev, [studentId]: status }));
  };

  const renderItem = ({ item }: { item: any }) => (
    <View style={styles.studentRow}>
      <Text style={styles.studentName}>{item.nama_lengkap}</Text>
      <View style={styles.pickerItemContainer}>
        <RNPickerSelect
          value={statuses[item.id]}
          onValueChange={(value) => handleStatusChange(item.id, value)}
          items={[
            { label: 'Hadir', value: 'Hadir' },
            { label: 'Izin', value: 'Izin' },
            { label: 'Sakit', value: 'Sakit' },
            { label: 'Alfa', value: 'Alfa' },
          ]}
          placeholder={{}}
          style={pickerSelectStyles}
        />
      </View>
    </View>
  );

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Icon source="arrow-left" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Absensi</Text>
      </View>
      
      <View style={styles.filterContainer}>
        <Button icon="calendar" mode="outlined" onPress={() => setShowDatePicker(true)}>
          {selectedDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}
        </Button>

        {isSuperUser && (
          <View style={styles.classPickerWrapper}>
            <RNPickerSelect
              onValueChange={(value) => setSelectedClass(value)}
              items={[
                { label: 'Kelas 1', value: '1' },
                { label: 'Kelas 2', value: '2' },
                { label: 'Kelas 3', value: '3' },
                { label: 'Kelas 4', value: '4' },
                { label: 'Kelas 5', value: '5' },
                { label: 'Kelas 6', value: '6' },
              ]}
              placeholder={{ label: 'Pilih Kelas', value: null }}
              value={selectedClass}
            />
          </View>
        )}
      </View>

      {showDatePicker && (
        <DateTimePicker
          value={selectedDate}
          mode="date"
          display="default"
          onChange={onDateChange}
        />
      )}

      {isLoading ? (
        <ActivityIndicator size="large" color="#007AFF" style={styles.loader} />
      ) : error ? (
        <View style={styles.centeredMessage}>
          <Text style={styles.errorText}>{error}</Text>
          <Button onPress={fetchAttendance}>Coba Lagi</Button>
        </View>
      ) : (
        <FlatList
          data={attendanceData}
          renderItem={renderItem}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={{ paddingBottom: 150 }}
          ListEmptyComponent={
            <View style={styles.centeredMessage}>
              <Text>Tidak ada siswa di kelas ini.</Text>
            </View>
          }
        />
      )}

      {!isLoading && !error && attendanceData.length > 0 && (
         <Button 
            mode="contained" 
            style={styles.saveButton}
            onPress={handleSave}
            loading={isSaving}
            disabled={isSaving}
          >
           Simpan Absensi
         </Button>
      )}

      <BottomNavBar />
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F2F2F7',
  },
  header: {
    padding: 20,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: '#E0E0E0',
    flexDirection: 'row', // Atur sebagai baris
    alignItems: 'center', // Pusatkan secara vertikal
    justifyContent: 'center', // Pusatkan teks di tengah
    position: 'relative', // Untuk posisi absolut tombol kembali
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#000',
    // marginHorizontal: 50, // Untuk memberi ruang di sekitar teks judul
  },
  backButton: {
    position: 'absolute',
    left: 15,
    padding: 5,
  },
  filterContainer: {
    padding: 15,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: '#E0E0E0',
  },
  classPickerWrapper: {
    flex: 1,
    marginLeft: 10,
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 5,
    justifyContent: 'center',
  },
  studentRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 10,
    paddingHorizontal: 15,
    backgroundColor: '#FFFFFF',
    borderRadius: 8,
    marginBottom: 10,
    marginHorizontal: 15,
    marginTop: 5,
  },
  studentName: {
    fontSize: 16,
    color: '#000',
    flex: 1,
  },
  pickerItemContainer: {
    width: 120, // Lebar dropdown
  },
  loader: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  centeredMessage: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 50,
  },
  errorText: {
    color: 'red',
    marginBottom: 10,
  },
  saveButton: {
    position: 'absolute',
    bottom: 70,
    left: 20,
    right: 20,
    padding: 5,
    borderRadius: 8,
  }
});

const pickerSelectStyles = StyleSheet.create({
  inputIOS: {
    fontSize: 16,
    paddingVertical: 12,
    paddingHorizontal: 10,
    color: 'black',
  },
  inputAndroid: {
    fontSize: 16,
    paddingHorizontal: 10,
    paddingVertical: 8,
    color: 'black',
  },
});

export default AbsensiScreen;
