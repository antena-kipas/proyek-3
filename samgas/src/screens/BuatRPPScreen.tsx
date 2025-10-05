import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, TextInput, ScrollView, KeyboardAvoidingView, Platform } from 'react-native';
import { Button } from 'react-native-paper';
import { useNavigation } from '@react-navigation/native';
import BottomNavBar from '../components/BottomNavBar';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RootStackParamList } from '../navigation/types';

type BuatRPPScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'BuatRPP'>;

const LabeledInput = ({ label }: { label: string }) => (
  <View style={styles.inputContainer}>
    <Text style={styles.label}>{label}</Text>
    <TextInput style={styles.input} />
  </View>
);

const BuatRPPScreen = () => {
  const navigation = useNavigation<BuatRPPScreenNavigationProp>();

  return (
    <View style={styles.container}>
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={styles.keyboardAvoidingContainer}
      >
        <ScrollView>
          <View style={styles.header}>
            <TouchableOpacity onPress={() => navigation.goBack()}>
              <Text style={styles.backButtonText}>←</Text>
            </TouchableOpacity>
            <Text style={styles.headerTitle}>RPP</Text>
            <Button mode="contained" onPress={() => console.log('Buat pressed')} style={styles.buatButton}>
              Buat
            </Button>
          </View>

          <View style={styles.formContainer}>
            <LabeledInput label="Mata Pelajaran" />
            <LabeledInput label="Topik Materi" />
            <LabeledInput label="Alokasi Waktu" />
            <LabeledInput label="Tujuan 1" />
            <LabeledInput label="Tujuan 2" />
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
      <BottomNavBar />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  keyboardAvoidingContainer: {
    flex: 1,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 50,
    paddingBottom: 20,
  },
  backButtonText: {
    fontSize: 30,
    color: '#000',
    fontWeight: 'bold',
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: 'black',
  },
  buatButton: {
    borderRadius: 20,
  },
  formContainer: {
    paddingHorizontal: 20,
  },
  inputContainer: {
    marginBottom: 20,
  },
  label: {
    fontSize: 16,
    color: 'gray',
    marginBottom: 5,
  },
  input: {
    borderBottomWidth: 1,
    borderBottomColor: 'lightgray',
    fontSize: 18,
    paddingVertical: 5,
  },
});

export default BuatRPPScreen;