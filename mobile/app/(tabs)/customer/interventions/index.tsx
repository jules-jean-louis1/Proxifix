import React, {useEffect, useState} from 'react';
import {View, StyleSheet, Text, FlatList} from 'react-native';
import {format} from "date-fns";
import {fr} from "date-fns/locale";
import {Feather} from "@expo/vector-icons";
import { useApi } from '@/app/utils/useApi';
import { useSessionContext } from '@/app/context/useSessionContext';
import CustomerHomeButton from '@/app/components/buttons/CustomerHomeButton';
import { AppointmentModalForm } from '@/app/components/appointment/AppointmentModalForm';

export default function InterventionsPage() {
    const api = useApi();
    const sessionCtx = useSessionContext();
    const sessionData = sessionCtx?.session;
    const [interventions, setInterventions] = useState<any>([]);
    const [equipements, setEquipments] = useState<any>([]);
    const [company, setCompanies] = useState<any>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<any>(null);

    useEffect(() => {
        (async() => {
            try {
                setLoading(true);
                setError(null);
                const response = await api.get(`/intervention/customer/${sessionData?.id}`)
                console.log('Interventions:', response.data);
                setInterventions(response.data);
                const equipementsResponse = await api.get(`/equipment/customer/${sessionData?.id}`);
                setEquipments(equipementsResponse.data);
                const companiesResponse = await api.get(`/company/all`)
                setCompanies(companiesResponse.data);
            } catch (error) {
                console.error('Error fetching interventions:', error);
                setError(error || 'Impossible to get interventions.');
            } finally {
                setLoading(false);
            }

        })();
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
                <AppointmentModalForm
                    type="create"
                    companies={company}
                    equipments={equipements}
                    setInterventions={setInterventions}
                />
                <Text style={styles.title}>Liste des interventions</Text>
                <Text style={styles.description}>
                    Vous avez
                    actuellement {interventions.length} intervention{interventions.length > 1 ? 's' : ''} enregistrée{interventions.length > 1 ? 's' : ''}.
                </Text>
            </View>
            <View style={styles.listContainer}>
                <FlatList
                    data={interventions}
                    keyExtractor={(intervention) => intervention.id}
                    renderItem={({item: intervention}) => (
                        <View style={styles.interventionItem}>
                            <Text style={styles.interventionTitle}>{intervention.title}</Text>

                            <View style={styles.containerInformation}>
                                <Feather name="briefcase" size={25} color="#000" style={styles.iconLeft}></Feather>
                                <Text style={styles.interventionDescription}> {intervention.company.name}</Text>
                            </View>

                            <View style={styles.containerInformation}>
                                <Feather name="package" size={25} color="#000" style={styles.iconLeft}></Feather>
                                {intervention.equipment ? (
                                    <Text style={styles.interventionDescription}>
                                        {intervention.equipment.name}
                                    </Text>
                                ) : (
                                    <Text style={styles.interventionDescription}>Aucun équipement associé</Text>
                                )}
                            </View>

                            <View style={styles.footerContainer}>
                                <View style={styles.dateContainer}>
                                    <Text style={styles.dateText}>
                                        {format(new Date(intervention.created_at), 'dd MMMM yyyy', {locale: fr})}
                                    </Text>
                                    <Feather name="calendar" size={17} color="#4BC0C0" style={styles.icon}/>
                                </View>

                                <Text style={styles.status}>
                                    {intervention.status.name}
                                </Text>

                                <Text style={styles.footerDate}>Déposé le : 19/10/35</Text>
                            </View>
                        </View>
                    )}
                />
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

