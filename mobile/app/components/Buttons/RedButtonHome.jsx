import React from 'react';
import {StyleSheet, View, Text, Pressable, Image} from "react-native";
import Flame from "@/app/assets/icons/flame.svg";
import {Feather} from "@expo/vector-icons";

const RedButtonHome = ({children, onPress}) => {
    return (
        <Pressable onPress={onPress} style={({pressed}) => [styles.container, pressed && styles.pressed]}>
            <View style={styles.buttonContent}>
                <Feather name="user" size={24} color="#fff" style={styles.icon} />
                <Text style={styles.text}>{children}</Text>
            </View>
        </Pressable>
    );
}

const styles = StyleSheet.create({
    container: {
        backgroundColor: '#E53953',
        width: 300,
        padding: 10,
        margin: 10,
        alignItems: "center",
        borderRadius: 8,
    },
    pressed: {
        opacity: 0.7,
    },
    buttonContent: {
        flexDirection: "row",
        alignItems: "center",
    },
    icon: {
        marginRight: 8
    },
    text: {
        color: "white",
    }
});

export default RedButtonHome;