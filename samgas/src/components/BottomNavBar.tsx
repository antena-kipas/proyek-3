import React from 'react';
import { View, StyleSheet } from 'react-native';
import { IconButton } from 'react-native-paper';



const BottomNavBar = () => {
  return (
    <View style={styles.container}>
      <IconButton
        icon="home-circle-outline"
        size={50}
        onPress={() => console.log('Home pressed')}
      />
      <IconButton
        icon="account-circle"
        size={50}
        onPress={() => console.log('Profile pressed')}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    height: 80,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: '#6495ED', // Warna biru seperti di mockup
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
  },
});

export default BottomNavBar;
