import React, { createContext, useState, useEffect, ReactNode } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';

// 1. Definisikan bentuk data User (Supaya TypeScript kenal)
interface UserData {
  id: number;
  name: string;
  email: string;
  role: string;
  kelas: string | null;
  mapel: string | null; // <-- DITAMBAHKAN
}

// 2. Masukkan userInfo ke dalam "Daftar Isi" Context
interface AuthContextData {
  userToken: string | null;
  userRole: string | null;
  userInfo: UserData | null; 
  isLoading: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => void;
}

export const AuthContext = createContext<AuthContextData>({} as AuthContextData);

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider = ({ children }: AuthProviderProps) => {
  const [userToken, setUserToken] = useState<string | null>(null);
  const [userRole, setUserRole] = useState<string | null>(null);
  const [userInfo, setUserInfo] = useState<UserData | null>(null); // <--- State baru
  const [isLoading, setIsLoading] = useState(true);

  // Pastikan URL ini benar (localhost jika pakai adb reverse)
  const API_URL = 'http://localhost:8000/api/login'; 

  const login = async (email: string, password: string) => {
    
    try {
      const response = await axios.post(API_URL, {
        email,
        password,
      });

      if (response.data.success) {
          const { access_token, user } = response.data;
          
          // Update State
          setUserToken(access_token);
          setUserRole(user.role);
          setUserInfo(user); // <--- Simpan data user
          
          // Simpan ke Storage HP
          await AsyncStorage.setItem('userToken', access_token);
          await AsyncStorage.setItem('userRole', user.role);
          await AsyncStorage.setItem('userData', JSON.stringify(user)); // <--- Simpan JSON
      } else {
          throw new Error(response.data.message || 'Login gagal');
      }

    } catch (error) {
      console.log('Login failed (handled):', error);
      throw error; 
    } finally {
      setIsLoading(false);
    }
  };

  const logout = async () => {
    setIsLoading(true);
    // Reset semua state
    setUserToken(null);
    setUserRole(null);
    setUserInfo(null); 
    try {
      // Hapus semua storage
      await AsyncStorage.removeItem('userToken');
      await AsyncStorage.removeItem('userRole');
      await AsyncStorage.removeItem('userData');
    } catch (error) {
      console.error('Logout failed (handled):', error);
    } finally {
      setIsLoading(false);
    }
  };

  const isLoggedIn = async () => {
    try {
      setIsLoading(true);
      const token = await AsyncStorage.getItem('userToken');
      const role = await AsyncStorage.getItem('userRole');
      const userData = await AsyncStorage.getItem('userData'); // <--- Ambil data user

      if (token) {
        setUserToken(token);
        setUserRole(role);
        if (userData) {
            setUserInfo(JSON.parse(userData)); // <--- Kembalikan ke format Object
        }
      }
    } catch (error) {
      console.error('isLoggedIn check failed:', error);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    isLoggedIn();
  }, []);

  return (
    // <--- JANGAN LUPA masukkan userInfo ke sini
    <AuthContext.Provider value={{ userToken, userRole, userInfo, isLoading, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
};