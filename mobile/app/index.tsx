import React from 'react';
import { View, Text, Image, StyleSheet } from 'react-native';
import { useRouter } from 'expo-router';
// import logo from '../assets/images/logo.png';
import { AppButton } from './components/buttons/AppButton';

export default function HomeScreen() {
  const router = useRouter();

  return (
    <View style={styles.container}>
      {/* <Image source={logo} style={{ marginBottom: 30 }} /> */}

      <Text style={styles.heading}>
        {'Votre '}
        <Text style={{ color: colors.secondary500 }}>App</Text>
        {'\n'}
        {"d'intervention en ligne"}
      </Text>

      {/* Registration and Login Buttons */}
      <View style={{ width: '85%' }}>
        <AppButton
          children="Espace Client"
          type="primary"
          icon={'account'}
          onPress={() => router.push({ pathname: '/(auth)/loginCustomer' })}
        />
        <AppButton
          children="Espace Technicien"
          type="tertiary"
          icon={'account'}
          onPress={() => router.push({ pathname: '/(auth)/loginAdmin' })}
        />
      </View>
    </View>
  );
}

const colors = {
  primary500: '#000000',
  secondary500: '#E53953',
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#FFFFFF',
  },
  heading: {
    fontSize: 45,
    fontWeight: 'bold',
    color: colors.primary500,
    fontFamily: 'Rubik-Bold.ttf',
    marginBottom: 60,
    textAlign: 'center',
  },
});
