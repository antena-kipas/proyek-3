import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Avatar, Card } from 'react-native-paper';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RootStackParamList } from '../navigation/types';
import { useNavigation } from '@react-navigation/native';
import BottomNavBar from '../components/BottomNavBar';

type RPPScreenNavigationProp = NativeStackNavigationProp<RootStackParamList, 'RPP'>;

const RPPScreen = () => {
  const navigation = useNavigation<RPPScreenNavigationProp>();

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        {/* Wrap Avatar.Icon in a Card to make it a rounded square */}
        <Card style={styles.card}>
          <Card.Content>
            <Avatar.Icon
              icon="file-document-outline"
              size={36} // Adjusted size to fit card padding
              color="#000"
              style={styles.icon} // Style for transparent background
            />
          </Card.Content>
        </Card>

        {/* Center Title */}
        <Text style={styles.headerTitle}>RPP</Text>

        {/* Right Back Button */}
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Text style={styles.backButtonText}>←</Text>
        </TouchableOpacity>
      </View>

      <Text style={styles.menuTitle}>Daftar Menu</Text>
      <TouchableOpacity style={styles.menuButton} onPress={() => navigation.navigate('BuatRPP')}>
        <Text style={styles.menuButtonText}>Buat RPP</Text>
      </TouchableOpacity>
      <TouchableOpacity style={styles.menuButton}>
        <Text style={styles.menuButtonText}>Daftar RPP</Text>
      </TouchableOpacity>
      <BottomNavBar />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingHorizontal: 20,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 50,
    marginBottom: 30,
    position: 'relative',
    height: 60, // Adjusted height for the card
  },
  card: {
    position: 'absolute',
    left: 0,
    width: 60,
    height: 60,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#E0E0E0',
    borderRadius: 15, // Rounded corners to match mockup
  },
  icon: {
    backgroundColor: 'transparent', // Make Avatar background transparent to show Card color
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    textAlign: 'center',
    color: 'black',
  },
  backButton: {
    position: 'absolute',
    right: 0,
    height: '100%',
    justifyContent: 'center',
    paddingHorizontal: 5,
  },
  backButtonText: {
    fontSize: 30,
    color: '#000',
    fontWeight: 'bold',
  },
  menuTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#3F51B5',
    marginBottom: 20,
  },
  menuButton: {
    backgroundColor: '#E0E0E0',
    padding: 15,
    borderRadius: 10,
    marginBottom: 15,
  },
  menuButtonText: {
    fontSize: 16,
    textAlign: 'center',
    color: '#000',
    fontWeight: 'bold',
  },
});

export default RPPScreen;
