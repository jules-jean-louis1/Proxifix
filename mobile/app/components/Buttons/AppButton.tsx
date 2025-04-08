import React from "react";
import { Button, ButtonProps } from "react-native-paper";
import { StyleSheet, TextStyle, ViewStyle } from "react-native";

interface AppButtonProps extends ButtonProps {
  type: "validate" | "cancel";
}

export const AppButton: React.FC<AppButtonProps> = ({
  children,
  type,
  ...props
}) => {
  const buttonStyle: ViewStyle =
    type === "validate" ? styles.validate : styles.cancel;
  const textStyle: TextStyle =
    type === "validate" ? styles.validateText : styles.cancelText;

  return (
    <Button style={buttonStyle} labelStyle={textStyle} {...props}>
      {children}
    </Button>
  );
};

const styles = StyleSheet.create({
  cancel: {
    backgroundColor: "#F0F3F4",
    width: "100%",
    paddingVertical: 15,
    marginVertical: 10,
    alignItems: "center",
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#F9556D",
  },
  validate: {
    backgroundColor: "#F9556D",
    width: "100%",
    paddingVertical: 15,
    marginVertical: 10,
    alignItems: "center",
    borderRadius: 8,
  },
  cancelText: {
    color: "#F9556D",
    fontFamily: "Rubik-Bold",
  },
  validateText: {
    color: "#FFFFFF",
    fontFamily: "Rubik-Bold",
  },
});
