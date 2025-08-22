import { Text, View, StyleSheet } from 'react-native';
import React from 'react';
import { AppButton } from '@/app/components/buttons/AppButton';
import { useSession } from '@/app/context/authContext';

const SettingsScreen = () => {
  const { signOut } = useSession();
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Paramètres</Text>
      <AppButton
        children="Deconnecter"
        type="secondary"
        onPress={() => {
          signOut();
        }}
      />
    </View>
  );
};
export default SettingsScreen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
  },
});
