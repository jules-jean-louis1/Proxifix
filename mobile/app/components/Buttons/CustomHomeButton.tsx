import React from "react";
import { Avatar, Button, ButtonProps, Text } from "react-native-paper";
import { StyleSheet, View } from "react-native";
import { useRouter } from "expo-router";

interface CustomHomeButtonProps extends Omit<ButtonProps, "children"> {
  title: string;
  subTitle: string;
  icon: string;
  route?: string;
}

const CustomHomeButton: React.FC<CustomHomeButtonProps> = ({
  style,
  title,
  subTitle,
  icon,
  route,
  ...props
}) => {
  const router = useRouter();
  return (
    <Button
      {...props}
      mode="elevated"
      onPress={() => route && router.push(route as any)}
      style={[styles.button, style]}
      contentStyle={styles.content}
    >
      <View style={styles.iconContainer}>
        <Avatar.Icon size={40} icon={icon} />
      </View>
      <View style={styles.textContainer}>
        <Text variant="titleMedium" style={styles.title}>
          {title}
        </Text>
        <Text variant="bodySmall" style={styles.subTitle}>
          {subTitle}
        </Text>
      </View>
    </Button>
  );
};

const styles = StyleSheet.create({
  button: {
    height: 100,
    borderRadius: 16,
    justifyContent: "center",
    width: '85%',
  },
  content: {
    flexDirection: "row", // Aligner les éléments horizontalement
    alignItems: "center", // Centrer verticalement
  },
  iconContainer: {
    marginRight: 16, // Espacement entre l'icône et le texte
  },
  textContainer: {
    flex: 1, // Prendre tout l'espace restant
  },
  title: {
    fontWeight: "bold",
  },
  subTitle: {
    color: "gray",
  },
});

export default CustomHomeButton;
