import React from 'react';
import {TouchableOpacity, View, Text, StyleSheet} from 'react-native';
import {useRouter} from 'expo-router';
import {Feather} from "@expo/vector-icons";

interface CustomerHomeProps {
    text: string,
    icon: any,
    url: any
}

const CustomerHomeButton: React.FC<CustomerHomeProps> = ({
                                text,
                                icon,
                                url,
                            }) => {
    const router = useRouter();

    return (
        <View style={styles.container}>
            <TouchableOpacity
                style={styles.button}
                onPress={() => router.push(url)}
            >
                <Feather name={icon} size={60} color="#007BFF" style={styles.icon}/>
                <Text style={styles.buttonText}>{text}</Text>
            </TouchableOpacity>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        flexDirection: 'column',
        justifyContent: 'center',
        alignItems: 'center',
        width: '100%',
    },
    button: {
        backgroundColor: '#007BFF',
        padding: 10,
        borderRadius: 10,
        flexDirection: 'column',
        alignItems: 'center',
        width: 180,
    },
    buttonText: {
        fontSize: 18,
        textTransform: 'uppercase',
        color: '#FFF',
    },
    icon: {
        marginRight: 10,
        color: '#FFF',
    },
});

export default CustomerHomeButton;