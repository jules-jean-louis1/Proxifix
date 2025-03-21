import {Text, TouchableOpacity, View, StyleSheet, Alert} from "react-native";
import {Feather} from "@expo/vector-icons";
import React from "react";
import axios from "axios";

const RegisterButton = ({
                           text = "S'inscrire",
                           icon = 'chevron-right',
                           url,
                           data,
                           successCallback,
                           errorCallback
                       }) => {
    const handleLogin = async () => {
        try {
            const response = await axios.post(url,data);
            if (response.status === 201) {
                successCallback(response);
            }
        } catch (error) {
            errorCallback(error);
        }
    };

    return (
        <TouchableOpacity onPress={handleLogin} style={styles.button}>
            <View style={styles.buttonContent}>
                <Text style={styles.buttonText}>{text}</Text>
                <Feather name={icon} size={24} color="#fff" style={styles.icon} />
            </View>
        </TouchableOpacity>
    )
}


const styles = StyleSheet.create({
    buttonContent: {
        gap: 5,
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
    },
    button: {
        backgroundColor: '#F9556D',
        fontWeight: 'bold',
        paddingVertical: 15,
        borderRadius: 10,
        marginTop: 30,
        width: '79%',
    },
    buttonText: {
        color: '#fff',
        textAlign: 'center',
    },
})

export default RegisterButton;