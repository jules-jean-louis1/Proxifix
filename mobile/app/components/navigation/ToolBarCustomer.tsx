import { useSession } from "@/app/context/ctx";
import { Feather } from "@expo/vector-icons";
import { View, StyleSheet, Pressable } from "react-native";
import { IconButton } from "react-native-paper";

export const ToolBarCustomer = () => {
  const { signOut } = useSession();
  return (
    <View style={style.container}>
      <View style={style.subContainer}>
        <Pressable
          onPress={() => signOut()}
          style={{
            backgroundColor: "#F5F5F8",
            padding: 10,
            borderRadius: 50,
            justifyContent: "center",
            alignItems: "center",
          }}
        >
          <Feather
            name="user"
            size={24}
            color="black"
          />
        </Pressable>
      </View>
    </View>
  );
};

const style = StyleSheet.create({
  container: {
    width: "100%",
    backgroundColor: "#fff",
    justifyContent: "center",
    alignItems: "center",
  },
  subContainer: {
    paddingVertical: 5,
    flexDirection: "row",
    justifyContent: "flex-end",
    width: "90%",
  },
});
