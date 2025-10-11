import React from 'react';
import { View, StyleSheet, TouchableOpacity } from 'react-native';
import { Card, Text, Avatar } from 'react-native-paper';
import BottomNavBar from '../components/BottomNavBar'; // Impor komponen

// Impor tipe props terpusat untuk layar ini
import { HomeScreenProps } from '../navigation/types';

const HomeScreen = ({ navigation }: HomeScreenProps) => {
  const menuItems = [
    { id: 'rpp', label: 'RPP', icon: 'file-document-outline'},
    { id: 'silabus', label: 'SILABUS', icon: 'file-chart-outline'},
    { id: 'absensi', label: 'Absensi', icon: 'account-check-outline'},
  ];

  return (
    <View style={styles.container}>
      <View style={styles.menuContainer}>
        {menuItems.map(item => (
          <TouchableOpacity
            key={item.id}
            style={styles.cardContainer}
            onPress={() => {
              if (item.id === 'rpp') {
                navigation.navigate('RPP');
              } else if (item.id === 'silabus') {
                navigation.navigate('Silabus');
              } else if (item.id === 'absensi') {
                navigation.navigate('Absensi');
              } else {
                console.log(`${item.label} pressed`);
              }
            }}>
            <Card style={styles.card}>
              <Card.Content>
                <Avatar.Icon
                  icon={item.icon}
                  size={50}
                  style={styles.icon}
                  color="#5D3FD3" // Warna ikon bisa disesuaikan
                />
              </Card.Content>
            </Card>
            <Text style={styles.label}>{item.label}</Text>
          </TouchableOpacity>
        ))}
      </View>
      <BottomNavBar />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFFFFF',
    paddingTop: 40, // Memberi jarak dari status bar
    paddingHorizontal: 10,
    paddingBottom: 80, // Tambahkan padding untuk memberi ruang bagi BottomNavBar
  },
  menuContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-around',
  },
  cardContainer: {
    alignItems: 'center',
    width: '40%', // Sekitar 40% untuk 2 kolom
    marginBottom: 25,
  },
  card: {
    width: 100,
    height: 100,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#F0F0F0', // Warna abu-abu muda seperti di mockup
  },
  icon: {
    backgroundColor: 'transparent',
  },
  label: {
    marginTop: 8,
    fontSize: 16,
    fontWeight: 'bold',
    textAlign: 'center',
  },
});

export default HomeScreen;