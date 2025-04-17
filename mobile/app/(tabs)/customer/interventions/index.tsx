import React, {useEffect, useState} from 'react';
import {View, StyleSheet, Text, FlatList} from 'react-native';
import CustomerHomeButton from "@/app/components/Buttons/CustomerHomeButton";
import AsyncStorage from "@react-native-async-storage/async-storage";
import axios from "axios";
import {format} from "date-fns";
import {fr} from "date-fns/locale";
import {Feather} from "@expo/vector-icons";

export default function Intervention() {
    const [interventions, setInterventions] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchInterventions = async () => {
            try {
                const token = await AsyncStorage.getItem('userToken');
                const userId = await AsyncStorage.getItem('userId');
                const response = await axios.get(`http://10.0.2.2:8000/api/intervention/user/${userId}`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                setInterventions(response.data);
            } catch (error) {
                console.error('Error fetching interventions:', error);
                setError(error.message || 'Impossible to get interventions.');
            } finally {
                setLoading(false);
            }
        };
        fetchInterventions();
    }, []);

    if (loading) {
        return (
            <View style={styles.container}>
                <Text>Chargement...</Text>
            </View>
        );
    }

    if (error) {
        return (
            <View style={styles.container}>
                <Text style={styles.errorText}>{error}</Text>
            </View>
        );
    }

    return (
        <View style={styles.container}>
            <View style={styles.header}>
                <Text style={styles.title}>Liste des interventions</Text>
                <Text style={styles.description}>
                    Vous avez
                    actuellement {interventions.length} intervention{interventions.length > 1 ? 's' : ''} enregistrée{interventions.length > 1 ? 's' : ''}.
                </Text>
            </View>

            <View style={styles.listContainer}>
                <FlatList
                    data={interventions}
                    keyExtractor={(item) => item.id.toString()}
                    renderItem={({item}) => (
                        <View style={styles.interventionItem}>
                            <Text style={styles.interventionTitle}>{item.title}</Text>

                            <View style={styles.containerInformation}>
                                <Feather name="briefcase" size={25} color="#000" style={styles.iconLeft}></Feather>
                                <Text style={styles.interventionDescription}> {item.company.name}</Text>
                            </View>

                            <View style={styles.containerInformation}>
                                <Feather name="package" size={25} color="#000" style={styles.iconLeft}></Feather>
                                {item.equipment && item.equipment.length > 0 ? (
                                    <Text style={styles.interventionDescription}>
                                        {item.equipment.map(eq => eq.name).join(", ")}
                                    </Text>
                                ) : (
                                    <Text style={styles.interventionDescription}>Aucun équipement associé</Text>
                                )}
                            </View>

                            <View style={styles.footerContainer}>
                                <View style={styles.dateContainer}>
                                    <Text style={styles.dateText}>
                                        {format(new Date(item.created_at), 'dd MMMM yyyy', {locale: fr})}
                                    </Text>
                                    <Feather name="calendar" size={17} color="#4BC0C0" style={styles.icon}/>
                                </View>

                                <Text style={styles.status}>
                                    {item.status.name}
                                </Text>

                                <Text style={styles.footerDate}>Déposé le : 19/10/35 {item.bookings}</Text>
                            </View>
                        </View>
                    )}
                />
            </View>

            <View style={styles.buttonContainer}>
                <CustomerHomeButton text="Equipements" icon="monitor" url="/equipments/list"/>
                <CustomerHomeButton text="Profil" icon="settings" url="/profile"/>
            </View>
        </View>
    )
        ;
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#F0F3F4',
        padding: 16,
    },
    header: {
        alignItems: 'center',
        marginBottom: 20,
    },
    title: {
        fontSize: 24,
        fontWeight: 'bold',
        color: '#344260',
        marginBottom: 10,
    },
    description: {
        fontSize: 16,
        color: '#5B6880',
    },
    listContainer: {
        flex: 1,
        marginBottom: 20,
    },
    interventionItem: {
        padding: 16,
        backgroundColor: '#fff',
        borderRadius: 8,
        marginBottom: 10,
        shadowColor: '#000',
        shadowOffset: {width: 0, height: 2},
        shadowOpacity: 0.2,
        shadowRadius: 2,
        elevation: 2,
    },
    interventionTitle: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#E53953',
        marginBottom: 5,
    },
    interventionDescription: {
        fontSize: 16,
        color: '#5B6880',
        marginBottom: 5,
    },
    containerInformation: {
        flex: 1,
        flexDirection: 'row',
        marginBottom: 5,
    },
    footerContainer: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginTop: 10,
    },
    dateContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        padding: 8,
        borderRadius: 8,
        marginRight: 8,
        justifyContent: 'flex-start',
    },
    dateText: {
        fontSize: 13,
        color: '#4BC0C0',
        fontWeight: 'bold',
    },
    icon: {
        paddingLeft: 5,
    },
    iconLeft: {
        marginRight: 10,
    },
    status: {
        fontSize: 13,
        fontWeight: 'bold',
        backgroundColor: 'rgba(0, 119, 193, 0.2)',
        color: '#48A3D7',
        padding: 8,
        borderRadius: 8,
        marginRight: 8,
        textAlign: 'center',
    },
    footerDate: {
        fontSize: 14,
        color: '#5B6880',
        flex: 1,
        textAlign: 'right',
    },
    buttonContainer: {
        flexDirection: 'row',
        justifyContent: 'space-evenly',
    },
    errorText: {
        color: 'red',
        fontSize: 18,
        textAlign: 'center',
    },
});

