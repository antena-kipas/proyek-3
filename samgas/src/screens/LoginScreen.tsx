import React, { useState, useContext } from 'react';
import { View, StyleSheet } from 'react-native';
import { TextInput, Button, Text, ActivityIndicator, HelperText } from 'react-native-paper'; // Tambahkan HelperText

// Impor konteks otentikasi
import { AuthContext } from '../context/AuthContext';

// Impor tipe props terpusat
import { LoginScreenProps } from '../navigation/types';

const LoginScreen = ({ navigation }: LoginScreenProps) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  
  // State untuk error spesifik per kolom
  const [emailError, setEmailError] = useState('');
  const [passwordError, setPasswordError] = useState('');

  const { login } = useContext(AuthContext);

  const handleLogin = async () => {
    setLoading(true);
    // 1. Reset error visual
    setEmailError('');
    setPasswordError('');

    try {
      await login(email, password);
    } catch (err: any) {
      console.log('RAW ERROR:', JSON.stringify(err, null, 2)); // Cek terminal

      // ============================================================
      // ZONA PENANGANAN ERROR KOMPREHENSIF
      // ============================================================

      // 1. Jika Error dari Axios (Response Server: 401, 403, 422, 500)
      if (err.response) {
        const status = err.response.status;
        const data = err.response.data;

        if (status === 401 || status === 403) {
          // Paksa muncul di kolom password
          setPasswordError('Email atau Password salah.');
        } 
        else if (status === 422 && data.errors) {
            // Validasi Laravel
            if (data.errors.email) setEmailError(data.errors.email[0]);
            if (data.errors.password) setPasswordError(data.errors.password[0]);
        } 
        else {
           // Error server lain (misal 500)
           const pesan = data.message || 'Terjadi kesalahan pada server.';
           setPasswordError(pesan);
        }
      } 
      // 2. Jika Error Manual dari AuthContext (throw new Error('...'))
      //    Ini yang sering terlewat!
      else if (err.message && !err.response) {
        setPasswordError(err.message); 
      }
      // 3. Jika Error Koneksi (Network Error / Server Down)
      else if (err.request) {
        setPasswordError('Gagal terhubung. Cek koneksi internet/IP.');
      } 
      // 4. Fallback Terakhir (Supaya HelperText TETAP MUNCUL apa pun yang terjadi)
      else {
        setPasswordError(JSON.stringify(err) || 'Terjadi error misterius.');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <Text variant="headlineLarge" style={styles.title}>
        Log In
      </Text>

      {/* INPUT EMAIL */}
      <TextInput
        label="Email yang sudah terdaftar"
        value={email}
        onChangeText={text => {
            setEmail(text); 
            setEmailError(''); // Hapus error saat mengetik
        }}
        mode="outlined"
        style={styles.input}
        keyboardType="email-address"
        autoCapitalize="none"
        disabled={loading}
        error={!!emailError} // Kolom jadi merah jika ada error
      />
      {/* Teks Error Kecil Merah untuk Email */}
      <HelperText type="error" visible={!!emailError}>
        {emailError}
      </HelperText>

      {/* INPUT PASSWORD */}
      <TextInput
        label="Password"
        value={password}
        onChangeText={text => {
            setPassword(text); 
            setPasswordError(''); // Hapus error saat mengetik
        }}
        mode="outlined"
        style={styles.input}
        secureTextEntry
        disabled={loading}
        error={!!passwordError} // Kolom jadi merah jika ada error
      />
      {/* Teks Error Kecil Merah untuk Password */}
      <HelperText type="error" visible={!!passwordError}>
        {passwordError}
      </HelperText>

      <Button
        mode="contained"
        onPress={handleLogin}
        style={styles.loginButton}
        labelStyle={styles.loginButtonLabel}
        disabled={loading}>
        {loading ? <ActivityIndicator animating={true} color="#fff" /> : 'Login'}
      </Button>

      

      <Button
        mode="text"
        onPress={() => console.log('Forgot Password Pressed')}
        style={styles.forgotPasswordButton}
        disabled={loading}>
        Lupa password
      </Button>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    paddingHorizontal: 20,
    backgroundColor: '#FFFFFF',
  },
  title: {
    textAlign: 'center',
    marginBottom: 30,
    fontWeight: 'bold',
  },
  input: {
    marginBottom: 0, // Ubah jadi 0 agar HelperText nempel di bawahnya
    marginTop: 10,
  },
  loginButton: {
    marginTop: 20,
    paddingVertical: 8,
    borderRadius: 10,
  },
  loginButtonLabel: {
    fontSize: 16,
  },
  forgotPasswordButton: {
    marginTop: 10,
  },
  // Style errorText yang lama sudah tidak diperlukan karena diganti HelperText
});

export default LoginScreen;