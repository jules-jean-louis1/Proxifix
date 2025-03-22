import React from 'react';
import { View, Text, StyleSheet } from 'react-native';

export const CustomerHome = () => {
    return (
        <View style={styles.container}>
            <Text>Bienvenue sur la page Home du client !</Text>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
    },
});

export default CustomerHome;