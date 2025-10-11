import React from 'react';
import { View, StyleSheet } from 'react-native';
import { IconButton } from 'react-native-paper';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RootStackParamList } from '../navigation/types';

// Definisikan tipe untuk navigasi agar 'navigate' memiliki auto-complete dan type-checking
type NavigationProp = NativeStackNavigationProp<RootStackParamList>;

const BottomNavBar = () => {
  const navigation = useNavigation<NavigationProp>();

  return (
    <View style={styles.container}>
      <IconButton
        icon="home-circle-outline"
        size={50}
        onPress={() => navigation.reset({ index: 0, routes: [{ name: 'Home' }] })}
      />
      <IconButton
        icon="account-circle"
        size={50}
        onPress={() => navigation.navigate('Profile')}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    height: 80,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: '#6495ED', // Warna biru seperti di mockup
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
  },
});

export default BottomNavBar;