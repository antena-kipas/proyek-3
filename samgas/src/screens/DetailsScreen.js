import React from 'react';
import { View, Text, StyleSheet } from 'react-native';

function DetailsScreen() {
  return (
    <View style={styles.container}>
      <Text style={styles.text}>Ini Halaman Detail</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, alignItems: 'center', justifyContent: 'center' },
  text: { fontSize: 24 },
});

export default DetailsScreen;