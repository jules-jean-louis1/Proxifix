// app/screens/Login.jsx
import React, {useState} from 'react';
import {View, Text, TextInput, TouchableOpacity, Alert, StyleSheet, Image} from 'react-native';
import axios from 'axios';
import logo from '../assets/images/logo_proaxive2.png';
import {useRouter} from "expo-router";
import AsyncStorage from "@react-native-async-storage/async-storage";


export default function LoginTechnicienForm() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const router = useRouter();

    const handleLogin = async () => {
        try {
            const response = await axios.post('http://10.0.2.2:8000/api/login', {email, password});
            if (response.status === 200) {
                await AsyncStorage.setItem('userToken', response.data.token);
                Alert.alert('Login réussi', 'Vous êtes connecté.');
                router.push('/profile');
            }
        } catch (error) {
            Alert.alert('Erreur', 'Problème de connexion. Veuillez réessayer.');
        }
    };


    return (
        <View style={styles.container}>
            <Image source={logo} style={styles.image}/>
            <View style={styles.form}>
                <Text style={styles.title}>Connexion à votre espace technicien en ligne</Text>
                {/* Fieldset pour l'input Mot de passe */}
                <View style={styles.fieldSet}>
                    <Text style={styles.legend}>Adresse email</Text>
                    <TextInput
                        style={styles.input}
                        placeholder="Entrez votre identifiant ou adresse email"
                        value={email}
                        onChangeText={setEmail}
                    />
                </View>

                {/* Fieldset pour l'input Email */}
                <View style={styles.fieldSet}>
                    <Text style={styles.legend}>Mot de passe</Text>
                    <TextInput
                        style={styles.input}
                        placeholder="Entrez votre mot de passe"
                        secureTextEntry={true}
                        value={password}
                        onChangeText={setPassword}
                    />
                </View>

                <Text style={styles.inline}>Votre accès est confidentiel, ne le communiquez jamais à autrui.</Text>
            </View>

            <TouchableOpacity onPress={handleLogin} style={styles.button}>
                <Text style={styles.buttonText}>Se connecter</Text>
            </TouchableOpacity>

            <View style={styles.containerBar}>
                <View style={styles.horizontalBar}/>
                <Text style={styles.text}>ou</Text>
                <View style={styles.horizontalBar}/>
            </View>

            <View style={styles.registerContainer}>
                <Text style={styles.registerText}>Si vous n’avez pas de compte</Text>
                <TouchableOpacity onPress={() => router.push('/registertechnicien')}>
                    <Text style={styles.register}>INSCRIVEZ-VOUS</Text>
                </TouchableOpacity>
            </View>

        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: '#F0F3F4',
    },
    image: {
        marginBottom: 20,
    },
    form: {
        width: '80%',
        padding: 20,
        backgroundColor: '#fff',
        borderRadius: 8,
        flexDirection: 'column',
    },
    fieldSet: {
        marginVertical: 10,
        paddingHorizontal: 10,
        paddingBottom: 10,
        borderWidth: 1,
        borderColor: '#344260',
        borderRadius: 5,
    },
    legend: {
        position: 'absolute',
        color: '#344260',
        top: -10,
        left: 10,
        fontWeight: 'regular',
        backgroundColor: '#fff',
        paddingHorizontal: 5,
    },
    title: {
        textAlign: 'center',
        fontSize: 20,
        fontWeight: 'bold',
        marginBottom: 20,
    },
    subtitle: {
        textAlign: 'center',
        fontSize: 16,
        marginTop: 20,
        marginBottom: 40,
    },
    inline: {
        textAlign: 'center',
        color: '#5B6880',
        fontSize: 14,
        marginVertical: 20,
    },
    input: {
        marginTop: 10,
        height: 40, // Less or equal 40 = doesnt show text android 💩
        borderWidth: 0,
        paddingLeft: 10,
        width: '100%',
    },
    button: {
        backgroundColor: '#F9556D',
        fontWeight: 'bold',
        paddingVertical: 20,
        borderRadius: 10,
        marginTop: 30,
        width: '79%',
    },
    buttonText: {
        color: '#fff',
        textAlign: 'center',
    },
    containerBar: {
        flexDirection: 'row',
        justifyContent: 'space-around',
        alignItems: 'center',
        marginTop: 30,
    },
    horizontalBar: {
        backgroundColor: '#637381',
        height: 1,
        width: '30%',
        marginHorizontal: 20,
    },
    text: {
        fontSize: 16,
        color: '#637381',
    },
    registerContainer: {
        alignItems: 'center',
        justifyContent: 'center',
        marginTop: 10,
    },
    registerText: {
        fontSize: 14,
        color: '#637381',
        marginBottom: 10,
    },
    register: {
        fontSize: 13,
        fontWeight: 'bold',
        textDecorationLine: 'underline',
        color: '#01358D',
    }
});
