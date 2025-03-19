import FontAwesome from '@expo/vector-icons/FontAwesome';
import {Tabs} from 'expo-router';
import {TabBar} from "@/app/navigation/TabBar";

export default function TabLayout() {
    return (
        <Tabs tabBar={(props) => <TabBar {...props} />}>
            <Tabs.Screen name="index" options={{title: 'Accueil'}}/>
            <Tabs.Screen name="interventions" options={{title: 'Interventions'}}/>
            <Tabs.Screen name="profile" options={{title: 'Profil'}}/>
        </Tabs>
    );
}
