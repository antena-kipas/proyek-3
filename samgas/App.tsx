import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { PaperProvider } from 'react-native-paper';
import { SafeAreaProvider } from 'react-native-safe-area-context';

// Impor tipe navigasi terpusat
import { RootStackParamList } from './src/navigation/types';

// Impor halaman
import LoginScreen from './src/screens/LoginScreen';
import HomeScreen from './src/screens/HomeScreen';


import RPPScreen from './src/screens/RPPScreen';
import BuatRPPScreen from './src/screens/BuatRPPScreen';
import ProfileScreen from './src/screens/ProfileScreen';
import DaftarRPPScreen from './src/screens/DaftarRPPScreen';
import SilabusScreen from './src/screens/SilabusScreen';
import BuatSilabusScreen from './src/screens/BuatSilabusScreen';
import DaftarSilabusScreen from './src/screens/DaftarSilabusScreen';
import AbsensiScreen from './src/screens/AbsensiScreen';
import BuatAbsensiScreen from './src/screens/BuatAbsensiScreen';
import RekapAbsensiScreen from './src/screens/RekapAbsensiScreen';
import TambahMuridScreen from './src/screens/TambahMuridScreen';
import DokumenAbsensiScreen from './src/screens/DokumenAbsensiScreen';
import HapusMuridScreen from './src/screens/HapusMuridScreen';

// Beritahu navigator tentang semua layar yang ada menggunakan tipe terpusat
const Stack = createNativeStackNavigator<RootStackParamList>();

function App(): React.JSX.Element {
  return (
    <SafeAreaProvider>
      <PaperProvider>
        <NavigationContainer>
          <Stack.Navigator initialRouteName="Login">
            <Stack.Screen name="Login" component={LoginScreen} options={{ headerShown: false }} />
            <Stack.Screen name="Home" component={HomeScreen} options={{ title: 'Beranda' }} />

            <Stack.Screen name="RPP" component={RPPScreen} options={{ headerShown: false }} />
            <Stack.Screen name="BuatRPP" component={BuatRPPScreen} options={{ headerShown: false }} />
            <Stack.Screen name="Profile" component={ProfileScreen} options={{ headerShown: false }} />
            <Stack.Screen name="DaftarRPP" component={DaftarRPPScreen} options={{ headerShown: false }} />
            <Stack.Screen name="Silabus" component={SilabusScreen} options={{ headerShown: false }} />
            <Stack.Screen name="BuatSilabus" component={BuatSilabusScreen} options={{ headerShown: false }} />
            <Stack.Screen name="DaftarSilabus" component={DaftarSilabusScreen} options={{ headerShown: false }} />
            <Stack.Screen name="Absensi" component={AbsensiScreen} options={{ headerShown: false }} />
            <Stack.Screen name="BuatAbsensi" component={BuatAbsensiScreen} options={{ headerShown: false }} />
            <Stack.Screen name="RekapAbsensi" component={RekapAbsensiScreen} options={{ headerShown: false }} />
            <Stack.Screen name="TambahMurid" component={TambahMuridScreen} options={{ headerShown: false }} />
            <Stack.Screen name="DokumenAbsensi" component={DokumenAbsensiScreen} options={{ headerShown: false }} />
            <Stack.Screen name="HapusMurid" component={HapusMuridScreen} options={{ headerShown: false }} />
          </Stack.Navigator>
        </NavigationContainer>
      </PaperProvider>
    </SafeAreaProvider>
  );
}

export default App;