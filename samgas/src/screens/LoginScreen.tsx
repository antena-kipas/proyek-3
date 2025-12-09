import React, { useState, useContext } from 'react';
import { View, StyleSheet } from 'react-native';
import { TextInput, Button, Text, ActivityIndicator } from 'react-native-paper';

// Impor konteks otentikasi
import { AuthContext } from '../context/AuthContext';

// Impor tipe props terpusat
import { LoginScreenProps } from '../navigation/types';

const LoginScreen = ({ navigation }: LoginScreenProps) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const { login } = useContext(AuthContext);

  const handleLogin = async () => {
    setLoading(true);
    setError('');
    try {
      await login(email, password);
      // Navigasi tidak lagi diperlukan di sini, RootNavigator akan menanganinya
    } catch (err: any) {
      if (err.response && err.response.data && err.response.data.message) {
        setError(err.response.data.message);
      } else {
        setError('Terjadi kesalahan. Silakan coba lagi.');
      }
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <Text variant="headlineLarge" style={styles.title}>
        Log In
      </Text>
      <TextInput
        label="Email yang sudah terdaftar"
        value={email}
        onChangeText={text => {setEmail(text); setError('');}}
        mode="outlined"
        style={styles.input}
        keyboardType="email-address"
        autoCapitalize="none"
        disabled={loading}
      />
      {error ? <Text style={styles.errorText}>{error}</Text> : null}
      <TextInput
        label="Password"
        value={password}
        onChangeText={text => {setPassword(text); setError('');}}
        mode="outlined"
        style={styles.input}
        secureTextEntry
        disabled={loading}
      />
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
    marginBottom: 15,
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
  errorText: {
    color: 'black', // Warna teks hitam
    backgroundColor: 'yellow', // Latar belakang kuning mencolok
    fontSize: 18, // Ukuran font lebih besar
    fontWeight: 'bold', // Tebal
    textAlign: 'center',
    marginBottom: 10,
    padding: 5,
    borderRadius: 5,
  },
});

export default LoginScreen;
