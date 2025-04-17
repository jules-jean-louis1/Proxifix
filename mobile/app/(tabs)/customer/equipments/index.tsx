// import { useEffect, useState } from "react";
// import AsyncStorage from "@react-native-async-storage/async-storage";
// import axios from "axios";
// import { FlatList, StyleSheet, Text, View } from "react-native";
// import { format } from "date-fns";
// import { fr } from "date-fns/locale";

// const EquipmentsList = () => {
//     const [equipments, setEquipments] = useState([]);
//     const [error, setError] = useState(null);
//     const [loading, setLoading] = useState(true);

//     useEffect(() => {
//         const fetchEquipments = async () => {
//             try {
//                 const token = await AsyncStorage.getItem('userToken');
//                 const userId = await AsyncStorage.getItem('userId');
//                 const response = await axios.get(`http://10.0.2.2:8000/api/equipment/user/${userId}`, {
//                     headers: {
//                         Authorization: `Bearer ${token}`,
//                     },
//                 });
//                 setEquipments(response.data);
//             } catch (error) {
//                 console.error('Error:', error.response ? error.response.data : error.message);
//                 setError(error.message || 'Impossible to get the equipments of user.');
//             } finally {
//                 setLoading(false);
//             }
//         };
//         fetchEquipments();
//     }, []);

//     if (loading) {
//         return (
//             <View style={styles.container}>
//                 <Text style={styles.loadingText}>Loading...</Text>
//             </View>
//         );
//     }

//     if (error) {
//         return (
//             <View style={styles.container}>
//                 <Text style={styles.errorText}>{error}</Text>
//             </View>
//         );
//     }

//     return (
//         <View style={styles.container}>
//             <Text style={styles.title}>Tous mes équipements</Text>
//             <FlatList
//                 data={equipments}
//                 keyExtractor={(item) => item.id.toString()}
//                 renderItem={({ item }) => (
//                     <View style={styles.equipmentItem}>
//                         <Text style={styles.equipmentName}>{item.name}</Text>
//                         <Text style={styles.equipmentBrand}>{item.brand.name}</Text>
//                         <Text style={styles.equipmentOS}>{item.operating_system.name}</Text>
//                         <Text style={styles.equipmentType}>{item.type_equipment.name}</Text>
//                         <Text style={styles.equipmentDate}>
//                             {format(new Date(item.created_at), 'dd MMMM yyyy à HH:mm', { locale: fr })}
//                         </Text>
//                     </View>
//                 )}
//             />
//         </View>
//     );
// };

// const styles = StyleSheet.create({
//     container: {
//         flex: 1,
//         padding: 16,
//         backgroundColor: '#F0F3F4',
//     },
//     title: {
//         textAlign: 'center',
//         fontSize: 24,
//         fontWeight: 'bold',
//         marginBottom: 20,
//         color: '#344260',
//     },
//     loadingText: {
//         textAlign: 'center',
//         fontSize: 18,
//         color: '#5B6880',
//     },
//     errorText: {
//         color: 'red',
//         fontSize: 18,
//         textAlign: 'center',
//     },
//     equipmentItem: {
//         padding: 16,
//         borderBottomWidth: 1,
//         borderBottomColor: '#ccc',
//         backgroundColor: '#fff',
//         borderRadius: 8,
//         marginBottom: 10,
//         shadowColor: '#000',
//         shadowOffset: { width: 0, height: 2 },
//         shadowOpacity: 0.2,
//         shadowRadius: 2,
//         elevation: 2,
//     },
//     equipmentName: {
//         fontSize: 20,
//         fontWeight: 'bold',
//         color: '#344260',
//         marginBottom: 5,
//     },
//     equipmentBrand: {
//         fontSize: 16,
//         color: '#5B6880',
//         marginBottom: 5,
//     },
//     equipmentOS: {
//         fontSize: 16,
//         color: '#5B6880',
//         marginBottom: 5,
//     },
//     equipmentType: {
//         fontSize: 16,
//         color: '#5B6880',
//         marginBottom: 5,
//     },
//     equipmentDate: {
//         fontSize: 14,
//         color: '#78849E',
//     },
// });

// export default EquipmentsList;
