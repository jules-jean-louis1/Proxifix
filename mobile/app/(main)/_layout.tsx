import { Tabs } from 'expo-router';

export default function MainLayout() {
    return (
        <Tabs>
            <Tabs.Screen name="home" options={{ title: 'Accueil', tabBarIcon: () => null }} />
            <Tabs.Screen name="profile" options={{ title: 'Profil', tabBarIcon: () => null }} />
            <Tabs.Screen name="settings" options={{ title: 'Paramètres', tabBarIcon: () => null }} />
        </Tabs>
    );
}