import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ActivityIndicator, TextInput, TouchableOpacity, ScrollView } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import SaveButton from "../components/Buttons/SaveButton";
import CancelButton from "../components/Buttons/CancelButton";
import axios from "axios";

const Profile = () => {
    const [userData, setUserData] = useState({ email: "", firstName: "", lastName: "", password: "", address: "", postalCode: "" });
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [activeTab, setActiveTab] = useState("Compte");

    useEffect(() => {
        const fetchUserData = async () => {
            try {
                const token = await AsyncStorage.getItem('userToken');
                if (!token) {
                    throw new Error('Token non trouvé');
                }

                const response = await axios.get('http://10.0.2.2:8000/api/profile', {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                setUserData(response.data);
            } catch (error) {
                console.error('Erreur lors de la récupération des données:', error);
                setError(error.message || 'Impossible de récupérer les données utilisateur.');
            } finally {
                setLoading(false);
            }
        };
        fetchUserData();
    }, []);

    const handleInputChange = (field, value) => {
        setUserData(prevState => ({
            ...prevState,
            [field]: value,
        }));
    };

    const handleSubmit = async () => {
        try {
            const token = await AsyncStorage.getItem('userToken');
            if (!token) {
                throw new Error('Token non trouvé');
            }

            const response = await axios.put('http://10.0.2.2:8000/api/profile', userData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });

            if (response.status === 200) {
                const newToken = response.data.token;
                if (newToken) {
                    await AsyncStorage.setItem('userToken', newToken);
                } else {
                    console.error('Le nouveau token est indéfini');
                }
                alert('Vos informations ont été mises à jour avec succès !');
            } else {
                alert('Erreur lors de la mise à jour des informations.');
            }
        } catch (error) {
            console.error('Erreur lors de la mise à jour des données:', error);
            setError(error.response?.data?.message || 'Impossible de mettre à jour les données utilisateur.');
        }
    };

    if (loading) {
        return <ActivityIndicator size="large" color="#0000ff" />;
    }
    if (error) {
        return <Text style={styles.error}>{error}</Text>;
    }

    return (
        <View style={styles.container}>
            <View style={styles.navigation}>
                <TouchableOpacity onPress={() => setActiveTab('Compte')}>
                    <Text style={[styles.navigationText, activeTab === 'Compte' && styles.activeTab]}>Compte</Text>
                </TouchableOpacity>
                <TouchableOpacity onPress={() => setActiveTab('Adresse')}>
                    <Text style={[styles.navigationText, activeTab === 'Adresse' && styles.activeTab]}>Adresse</Text>
                </TouchableOpacity>
            </View>
            <ScrollView contentContainerStyle={styles.form}>
                {activeTab === 'Compte' && (
                    <>
                        <Text style={styles.subtitle}>Informations de votre compte utilisateur</Text>
                        <Text style={styles.text}>Mettre à jour vos informations</Text>

                        <View style={styles.fieldSet}>
                            <Text style={styles.legend}>Adresse email</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="Email"
                                onChangeText={(value) => handleInputChange('email', value)}
                                value={userData.email}
                            />
                        </View>

                        <View style={styles.fieldSet}>
                            <Text style={styles.legend}>Nom</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="Nom"
                                onChangeText={(value) => handleInputChange('lastName', value)}
                                value={userData.lastName}
                            />
                        </View>

                        <View style={styles.fieldSet}>
                            <Text style={styles.legend}>Prénom</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="Prénom"
                                onChangeText={(value) => handleInputChange('firstName', value)}
                                value={userData.firstName}
                            />
                        </View>

                        <View style={styles.fieldSet}>
                            <Text style={styles.legend}>Téléphone</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="Téléphone"
                                onChangeText={(value) => handleInputChange('phoneNumber', value)}
                                value={"a changer"}
                            />
                        </View>
                    </>
                )}
                {activeTab === 'Adresse' && (
                    <>
                        <Text style={styles.subtitle}>Modifier mon adresse postale</Text>
                        <Text style={styles.text}>Si vous avez changé d'adresse postale, il est important de la mettre à jour via le formulaire ci-dessous.</Text>

                        <View style={styles.fieldSet}>
                            <Text style={styles.legend}>Adresse postale</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="Adresse postale"
                                onChangeText={(value) => handleInputChange('address', value)}
                                value={userData.address}
                            />
                        </View>

                        <View style={styles.fieldSet}>
                            <Text style={styles.legend}>Code postal</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="Code postal"
                                onChangeText={(value) => handleInputChange('postalCode', value)}
                                value={userData.postalCode}
                            />
                        </View>
                    </>
                )}

                <CancelButton>Annuler</CancelButton>
                <SaveButton onPress={handleSubmit}>Enregistrer</SaveButton>
            </ScrollView>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#F0F3F4',
    },
    navigation: {
        height: 50,
        flexDirection: 'row',
        justifyContent: 'space-evenly',
        alignItems: 'center',
        borderBottomWidth: 1,
        borderBottomColor: '#ddd',
    },
    navigationText: {
        fontFamily: 'Rubik-Bold.ttf',
        fontWeight: 'bold',
        color: '#5B6880',
    },
    activeTab: {
        color: '#F9556D',
        borderBottomWidth: 2,
        borderBottomColor: '#F9556D',
    },
    form: {
        flexGrow: 1,
        padding: 20,
        backgroundColor: '#fff',
    },
    subtitle: {
        marginBottom: 10,
        textAlign: 'center',
        fontSize: 14,
        color: '#344260',
        fontFamily: 'Outfit-Bold.ttf',
        fontWeight: 'bold',
    },
    text: {
        marginBottom: 20,
        textAlign: 'center',
        fontSize: 12,
        color: '#344260',
        fontFamily: 'Outfit-Regular.ttf',
        fontWeight: 'regular',
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
    input: {
        height: 40,
        borderWidth: 0,
        paddingLeft: 10,
        width: '100%',
    },
    error: {
        color: 'red',
        textAlign: 'center',
        marginTop: 20,
    },
});

export default Profile;
