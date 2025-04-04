import { useSession } from "@/app/context/ctx";
import { View, StyleSheet } from "react-native";
import { IconButton } from "react-native-paper";

export const ToolBarCustomer = () => {
    const { signOut } = useSession();
  return (
    <View style={style.container}>
      <IconButton
        icon="account"
        size={20}
        onPress={() => signOut()}
      />
    </View>
  );
};

const style = StyleSheet.create({
    container : {
        flexDirection: "row",
        justifyContent: "flex-end"
    }
})