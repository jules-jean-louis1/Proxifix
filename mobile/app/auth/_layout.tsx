import {Stack} from 'expo-router/stack';
import {Tabs} from "expo-router";

export default function AuthLayout() {
    return (
        <Stack>
            <Tabs.Screen name="login" options={{title: 'Connexion'}}/>
            <Tabs.Screen name="register" options={{title: 'Inscription'}}/>
            <Tabs.Screen name="registertechnicien" options={{title: 'Inscription technicien'}}/>
            <Tabs.Screen name="technicien" options={{title: 'Connexion technicien'}}/>

        </Stack>
    );
}
