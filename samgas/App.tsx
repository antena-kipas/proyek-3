import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { PaperProvider } from 'react-native-paper';

// Impor tipe navigasi terpusat
import { RootStackParamList } from './src/navigation/types';

// Impor halaman
import LoginScreen from './src/screens/LoginScreen';
import HomeScreen from './src/screens/HomeScreen';
import DetailsScreen from './src/screens/DetailsScreen';

import RPPScreen from './src/screens/RPPScreen';
import BuatRPPScreen from './src/screens/BuatRPPScreen';

// Beritahu navigator tentang semua layar yang ada menggunakan tipe terpusat
const Stack = createNativeStackNavigator<RootStackParamList>();

function App(): React.JSX.Element {
  return (
    <PaperProvider>
      <NavigationContainer>
        <Stack.Navigator initialRouteName="Login">
          <Stack.Screen name="Login" component={LoginScreen} options={{ headerShown: false }} />
          <Stack.Screen name="Home" component={HomeScreen} options={{ title: 'Beranda' }} />
          <Stack.Screen name="Details" component={DetailsScreen} options={{ title: 'Detail' }} />
          <Stack.Screen name="RPP" component={RPPScreen} options={{ headerShown: false }} />
          <Stack.Screen name="BuatRPP" component={BuatRPPScreen} options={{ headerShown: false }} />
        </Stack.Navigator>
      </NavigationContainer>
    </PaperProvider>
  );
}

export default App;