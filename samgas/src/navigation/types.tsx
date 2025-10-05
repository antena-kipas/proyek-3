import type { NativeStackScreenProps } from '@react-navigation/native-stack';

// Definisikan semua layar dan parameter yang mungkin ada di aplikasi Anda
export type RootStackParamList = {
  Login: undefined; // Tidak ada parameter yang dikirim ke layar Login
  Home: undefined;    // Tidak ada parameter yang dikirim ke layar Home
  Details: { userId: string }; // Contoh jika layar Detail butuh userId
  RPP: undefined; // Tidak ada parameter yang dikirim ke layar RPP
  BuatRPP: undefined;
};

// Ekspor tipe props untuk setiap layar agar mudah digunakan di komponen lain
export type LoginScreenProps = NativeStackScreenProps<RootStackParamList, 'Login'>;
export type HomeScreenProps = NativeStackScreenProps<RootStackParamList, 'Home'>;
export type DetailsScreenProps = NativeStackScreenProps<RootStackParamList, 'Details'>;
