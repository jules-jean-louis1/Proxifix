import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert, StyleSheet, Image } from 'react-native';
import axios from 'axios';
import {useRouter} from "expo-router";
// import logo from "../assets/images/logo_proaxive2.png";
import {Feather} from "@expo/vector-icons";
import RegisterButton from "@/app/components/Buttons/RegisterButton";
import Constants from 'expo-constants';

export default function RegisterForm() {
    const [email, setEmail] = useState('');
    const [firstName, setFirstName] = useState('');
    const [lastName, setLastName] = useState('');
    const [password, setPassword] = useState('');
    const router = useRouter();
    const API_ENDPOINT = Constants.expoConfig?.extra?.API_ENDPOINT;


    const handleSuccess = (response) => {
        Alert.alert('Inscription réussie', 'Votre compte a été créé.');
    };

    const handleError = (error) => {
        Alert.alert('Erreur', 'Problème lors de l\'inscription.');
    };

    return (
        <View style={styles.container}>
            {/* <Image source={logo} style={styles.image} /> */}
            <View style={styles.form}>
                <Text style={styles.title}>Créer votre espace client</Text>

                <View style={styles.fieldSet}>
                    <Text style={styles.legend}>Adresse email</Text>
                    <TextInput
                        style={styles.input}
                        placeholderTextColor="#344260"
                        placeholder="Entrez votre adresse email"
                        value={email}
                        onChangeText={setEmail}
                    />
                </View>
                <View style={styles.fieldSet}>
                    <Text style={styles.legend}>Prénom</Text>
                    <TextInput
                        style={styles.input}
                        placeholderTextColor="#344260"
                        placeholder="Entrez votre prénom"
                        value={firstName}
                        onChangeText={setFirstName}
                    />
                </View>
                <View style={styles.fieldSet}>
                    <Text style={styles.legend}>Nom</Text>
                    <TextInput
                        style={styles.input}
                        placeholderTextColor="#344260"
                        placeholder="Entrez votre nom"
                        value={lastName}
                        onChangeText={setLastName}
                    />
                </View>

                <View style={styles.fieldSet}>
                    <Text style={styles.legend}>Mot de passe</Text>
                    <TextInput
                        style={styles.input}
                        placeholderTextColor="#344260"
                        placeholder="Entrez votre mot de passe"
                        secureTextEntry={true}
                        value={password}
                        onChangeText={setPassword}
                    />
                </View>
            </View>

            <RegisterButton
                url={`${API_ENDPOINT}/auth/customer/register`}
                data={{ email, first_name: firstName, last_name: lastName, password }}
                successCallback={handleSuccess}
                errorCallback={handleError}
            />

            <View style={styles.containerBar}>
                <View style={styles.horizontalBar}/>
                <Text style={styles.text}>ou</Text>
                <View style={styles.horizontalBar}/>
            </View>

            <View style={styles.loginContainer}>
                <Text style={styles.loginText}>Si vous avez déjà un compte</Text>
                <TouchableOpacity onPress={() => router.push('/auth/login')}>
                    <Text style={styles.login}>CONNECTEZ-VOUS</Text>
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
    input: {
        height: 40,
        borderWidth: 0,
        paddingLeft: 10,
        width: '100%',
        color: '#000',
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
    loginContainer: {
        alignItems: 'center',
        justifyContent: 'center',
        marginTop: 10,
    },
    loginText: {
        fontSize: 14,
        color: '#637381',
        marginBottom: 10,
    },
    login: {
        fontSize: 13,
        fontWeight: 'bold',
        textDecorationLine: 'underline',
        color: '#01358D',
    },
});
