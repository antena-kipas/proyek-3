import React, { useState } from 'react';
import { View, StyleSheet } from 'react-native';
import { TextInput, Button, Text } from 'react-native-paper';

// Impor tipe props terpusat untuk layar ini
import { LoginScreenProps } from '../navigation/types';

const LoginScreen = ({ navigation }: LoginScreenProps) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  return (
    <View style={styles.container}>
      <Text variant="headlineLarge" style={styles.title}>
        Log In
      </Text>
      <TextInput
        label="Email yang sudah terdaftar"
        value={email}
        onChangeText={setEmail}
        mode="outlined"
        style={styles.input}
        keyboardType="email-address"
        autoCapitalize="none"
      />
      <TextInput
        label="Password"
        value={password}
        onChangeText={setPassword}
        mode="outlined"
        style={styles.input}
        secureTextEntry
      />
      <Button
        mode="contained"
        onPress={() => navigation.replace('Home')} // Diubah untuk navigasi
        style={styles.loginButton}
        labelStyle={styles.loginButtonLabel}>
        Login
      </Button>
      <Button
        mode="text"
        onPress={() => console.log('Forgot Password Pressed')}
        style={styles.forgotPasswordButton}>
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
});

export default LoginScreen;
