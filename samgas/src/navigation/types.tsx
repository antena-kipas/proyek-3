import type { NativeStackScreenProps } from '@react-navigation/native-stack';

// Definisikan semua layar dan parameter yang mungkin ada di aplikasi Anda
export type RootStackParamList = {
  Login: undefined; // Tidak ada parameter yang dikirim ke layar Login
  Home: undefined;    // Tidak ada parameter yang dikirim ke layar Home
  RPP: undefined; // Tidak ada parameter yang dikirim ke layar RPP
  BuatRPP: undefined;
  Profile: undefined;
  DaftarRPP: undefined;
  Silabus: undefined;
  BuatSilabus: undefined;
  DaftarSilabus: undefined;
  Absensi: undefined;
  BuatAbsensi: undefined;
  RekapAbsensi: undefined;
  TambahMurid: undefined;
  DokumenAbsensi: undefined;
  HapusMurid: undefined;
};

// Ekspor tipe props untuk setiap layar agar mudah digunakan di komponen lain
export type LoginScreenProps = NativeStackScreenProps<RootStackParamList, 'Login'>;
export type HomeScreenProps = NativeStackScreenProps<RootStackParamList, 'Home'>;

