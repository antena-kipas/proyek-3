import React, { useState } from 'react';
import { StyleSheet, View, ScrollView, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Text, Icon, TextInput } from 'react-native-paper';
import { RootStackParamList } from '../navigation/types';

type BuatAbsensiScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'BuatAbsensi'>;


const AbsensiData = [
  { id: 1, nama: 'Nomor 2', kelas: 'V' },
  { id: 2, nama: 'Ujang', kelas: 'V' },
  { id: 3, nama: 'NoName', kelas: 'V' },
  { id: 4, nama: 'Anonymous', kelas: 'V' },
  { id: 5, nama: 'Antena-kipas', kelas: 'V' },
  { id: 6, nama: 'Shadow', kelas: 'V' },
];

const user = {
    nama: 'JAMAL',
    kelas: 'V'
};

// --- Attendance Button Types ---
type Status = 'H' | 'S' | 'I' | 'A';
const STATUS_OPTIONS: { status: Status; color: string }[] = [
    { status: 'H', color: '#4CAF50' }, // Green
    { status: 'S', color: '#FFC107' }, // Yellow
    { status: 'I', color: '#2196F3' }, // Blue
    { status: 'A', color: '#F44336' }, // Red
];


// --- KOMPONEN ---

const Header = ({ onBackPress }: { onBackPress: () => void }) => (
  <View style={styles.header}>
    <View style={styles.headerTop}>
      <TouchableOpacity onPress={onBackPress}>
        <Icon source="arrow-left" size={28} color="#FFFFFF" />
      </TouchableOpacity>
      <Text style={styles.headerTitle}>Daftar Absensi</Text>
      <View style={{ width: 28 }} /> 
    </View>
    <UserInfo />
  </View>
);

const UserInfo = () => (
    <View style={styles.userInfo}>
        <View>
            <Text style={styles.userInfoText}>Nama Guru : </Text>
            <Text style={styles.userInfoText}>Kelas           :</Text>
        </View>
        <View>
            <Text style={styles.userInfoText}>{user.nama}</Text>
            <Text style={styles.userInfoText}>{user.kelas}</Text>
        </View>
    </View>
);

const SearchBar = () => {
  const [searchQuery, setSearchQuery] = React.useState('');
  return (
    <View style={styles.searchContainer}>
      <TextInput
        label="Cari nama siswa"
        value={searchQuery}
        onChangeText={setSearchQuery}
        style={styles.searchInput}
        mode="outlined"
        left={<TextInput.Icon icon="magnify" />}
      />
    </View>
  );
};

const AttendanceButtons = ({ selectedStatus, onSelect }: { selectedStatus: Status | '' | undefined, onSelect: (status: Status) => void }) => (
    <View style={styles.iconContainer}>
        {STATUS_OPTIONS.map(({ status, color }) => {
            const isSelected = selectedStatus === status;
            return (
                <TouchableOpacity 
                    key={status}
                    style={[
                        styles.statusButton, 
                        { backgroundColor: isSelected ? color : '#E0E0E0' }
                    ]}
                    onPress={() => onSelect(status)}
                >
                    <Text style={[styles.statusButtonText, { color: isSelected ? '#FFFFFF' : '#000000' }]}>{status}</Text>
                </TouchableOpacity>
            );
        })}
    </View>
);

const AbsensiItem = ({ item, isLastItem, status, onStatusChange }: { item: typeof AbsensiData[0], isLastItem: boolean, status: Status | '' | undefined, onStatusChange: (id: number, status: Status) => void }) => (
  <View>
    <View style={styles.absensiItem}>
      <View style={styles.absensiInfo}>
        <Text style={styles.absensiSubject}>Nama: {item.nama}</Text>
        <Text style={styles.absensiDetail}>kelas: {item.kelas}</Text>
      </View>
      <AttendanceButtons selectedStatus={status} onSelect={(newStatus) => onStatusChange(item.id, newStatus)} />
    </View>
    {!isLastItem && <View style={styles.separator} />}
  </View>
);

import BottomNavBar from '../components/BottomNavBar';

// --- SCREEN UTAMA ---

const BuatAbsensiScreen = () => {
  const navigation = useNavigation<BuatAbsensiScreenNavigationProp>();
  const [attendanceStatus, setAttendanceStatus] = useState<{[key: number]: Status | ''}>({});

  const handleStatusChange = (studentId: number, status: Status) => {
    setAttendanceStatus(prev => ({
      ...prev,
      [studentId]: prev[studentId] === status ? '' : status, // Toggle on/off
    }));
  };

  return (
    <SafeAreaView style={styles.container}>
      <Header onBackPress={() => navigation.goBack()} />
      <SearchBar />
      <ScrollView contentContainerStyle={styles.scrollViewContent}>
        {AbsensiData.map((item, index) => (
          <AbsensiItem 
            key={item.id} 
            item={item} 
            isLastItem={index === AbsensiData.length - 1}
            status={attendanceStatus[item.id]}
            onStatusChange={handleStatusChange}
          />
        ))}
      </ScrollView>
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
  // ScrollView
  scrollViewContent: {
    paddingHorizontal: 16,
    paddingTop: 10,
    paddingBottom: 80, // Space for footer
  },
  // Absensi Item
  absensiItem: {
    paddingVertical: 12,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  absensiInfo: {
    flex: 1,
  },
  absensiSubject: {
    fontSize: 15,
    fontWeight: '600',
    color: '#000',
    marginBottom: 4,
  },
  absensiDetail: {
    fontSize: 14,
    color: '#3C3C43',
    lineHeight: 20,
  },
  iconContainer: {
    flexDirection: 'row',
  },
  statusButton: {
    width: 30,
    height: 30,
    borderRadius: 15,
    justifyContent: 'center',
    alignItems: 'center',
    marginHorizontal: 4,
  },
  statusButtonText: {
    fontWeight: 'bold',
    fontSize: 14,
  },
  separator: {
    height: 1,
    backgroundColor: '#D1D1D6',
  },
});

export default BuatAbsensiScreen;
