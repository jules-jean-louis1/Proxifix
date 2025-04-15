import React from "react";
import { Button, ButtonProps } from "react-native-paper";
import { StyleSheet, TextStyle, ViewStyle } from "react-native";

interface AppButtonProps extends ButtonProps {
  type: "primary" | "secondary" | "tertiary";
}

export const AppButton: React.FC<AppButtonProps> = ({
  children,
  type,
  ...props
}) => {
  const buttonStyle: ViewStyle = styles[type];
  const textStyle: TextStyle = styles[`${type}Text`];

  return (
    <Button style={buttonStyle} labelStyle={textStyle} {...props}>
      {children}
    </Button>
  );
};

const styles = StyleSheet.create({
  primary: {
    backgroundColor: "#F9556D",
    width: "100%",
    paddingVertical: 15,
    marginVertical: 10,
    alignItems: "center",
    borderRadius: 8,
  },
  primaryText: {
    color: "#FFFFFF",
    fontFamily: "Rubik-Bold.ttf",
  },
  secondary: {
    backgroundColor: "#F0F3F4",
    width: "100%",
    paddingVertical: 15,
    marginVertical: 10,
    alignItems: "center",
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#F9556D",
  },
  secondaryText: {
    color: "#F9556D",
    fontFamily: "Rubik-Bold.ttf",
  },
  tertiary: {
    backgroundColor: "#01358D",
    width: "100%",
    paddingVertical: 15,
    marginVertical: 10,
    alignItems: "center",
    borderRadius: 8,
  },
  tertiaryText: {
    color: "#FFFFFF",
    fontFamily: "Rubik-Bold.ttf",
  },
});
