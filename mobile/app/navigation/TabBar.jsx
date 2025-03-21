import {View, StyleSheet} from 'react-native';
import {useLinkBuilder, useTheme} from '@react-navigation/native';
import {Text, PlatformPressable} from '@react-navigation/elements';
import {Feather} from "@expo/vector-icons";

export function TabBar({state, descriptors, navigation}) {
    const {colors} = useTheme();
    const {buildHref} = useLinkBuilder();

    // Handle icons with route name
    const icon = {
        index: (props) => <Feather name="home" size={24} {...props} />,
        interventions: (props) => <Feather name="tool" size={24} {...props} />,
        profile: (props) => <Feather name="settings" size={24} {...props} />,
    }

    // stock current route name
    const currentRoute = state.routes[state.index].name;

    // we don't want tabbar on index/home page
    if (currentRoute === 'index') return null;

    return (
        <View style={styles.tabbar}>
            {state.routes.map((route, index) => {
                const {options} = descriptors[route.key];

                const label =
                    options.tabBarLabel !== undefined
                        ? options.tabBarLabel
                        : options.title !== undefined
                            ? options.title
                            : route.name;

                const isFocused = state.index === index;

                const onPress = () => {
                    const event = navigation.emit({
                        type: 'tabPress',
                        target: route.key,
                        canPreventDefault: true,
                    });

                    if (!isFocused && !event.defaultPrevented) {
                        navigation.navigate(route.name, route.params);
                    }
                };

                const onLongPress = () => {
                    navigation.emit({
                        type: 'tabLongPress',
                        target: route.key,
                    });
                };

                return (
                    <PlatformPressable
                        key={route.key}
                        href={buildHref(route.name, route.params)}
                        accessibilityState={isFocused ? {selected: true} : {}}
                        accessibilityLabel={options.tabBarAccessibilityLabel}
                        onPress={onPress}
                        onLongPress={onLongPress}
                        style={[styles.tabbarItem, isFocused]
                    }>
                        <View style={[
                            styles.iconContainer,
                            isFocused && styles.focusedIconContainer
                        ]}>
                            {icon[route.name]({
                                color: isFocused,
                            })}
                        </View>
                        <Text style={[
                            { color: '#000' },
                            styles.label
                        ]}>
                            {label}
                        </Text>
                    </PlatformPressable>
                );
            })}
        </View>
    );
}

const styles = StyleSheet.create({
    tabbar: {
        width: '100%',
        height: 80,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-around',
        backgroundColor: '#fff',
        borderColor: '#00000033',
        borderTopWidth: 1,
    },
    tabbarItem: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: '#fff',
        gap: 2,

    },
    iconContainer: {
        paddingVertical: 8,
        paddingHorizontal: 25,
        borderRadius: 20,
    },
    focusedIconContainer: {
        backgroundColor: '#FFD1DC',
    },
    label: {
        fontWeight: 'bold',
        marginBottom: 8,
    }
})