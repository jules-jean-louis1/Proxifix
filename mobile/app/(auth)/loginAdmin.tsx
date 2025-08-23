import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { useRouter } from 'expo-router';
import { LoginForm } from '../components/auth/LoginForm';
import { Divider } from 'react-native-paper';
import { ToolBarCustomer } from '@/app/components/customer/navigation/ToolBarCustomer';

export default function LoginCustomer() {
  const [success, setSuccess] = useState<boolean | null>(null);
  const router = useRouter();

  useEffect(() => {
    if (success === false) return;
    if (success) {
      router.replace('../admin');
    }
  }, [success]);

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title={'Espace Technicien'}
        bottomBar
        showBack
        onBackPress={() => router.push('/')}
      />
      <View style={styles.container}>
        <View style={styles.form}>
          <Text style={styles.title}>
            Connexion à votre espace technicien en ligne
          </Text>
          <LoginForm success={success} setSuccess={setSuccess} />
          <Text style={styles.inline}>
            Votre accès est confidentiel, ne le communiquez jamais à autrui.
          </Text>
        </View>
        <View style={styles.containerBar}>
          <Divider />
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#F0F3F4',
  },
  form: {
    width: '90%',
    padding: 20,
    backgroundColor: '#fff',
    borderRadius: 8,
    flexDirection: 'column',
  },
  title: {
    textAlign: 'center',
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 20,
  },

  inline: {
    textAlign: 'center',
    color: '#5B6880',
    fontSize: 14,
    marginVertical: 20,
  },
  containerBar: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    alignItems: 'center',
    marginTop: 30,
  },
});
