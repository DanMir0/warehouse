<script setup>
import {ref} from "vue";

const props = defineProps({
    title: String,
    btnIcon: String,
    headers: Array,
    items: Array,
    editedItem: Object,
});

const dialogDelete = defineModel()

let search = ref('');

const emit = defineEmits(["add-item", "delete-item-confirm", "edit-item"]);

const editedIndex = ref(-1);

function closeDelete() {
    dialogDelete.value = false;
}

function deleteItem(item) {
    if (item.id) {
        console.log()
        editedIndex.value = props.items.indexOf(item);
        Object.assign(props.editedItem, item)
        dialogDelete.value = true
    }
}


</script>

<template>
    <v-data-table
        :headers="headers"
        :items="items"
        :items-per-page-text="'Строк на странице:'"
        class="elevation-1"
        :items-per-page-options="[
              {value: 10, title: '10'},
              {value: 25, title: '25'},
              {value: 50, title: '50'},
              {value: 100, title: '100'},
              {value: -1, title: 'Все'}
            ]"
        :sort-by="[{key: 'name', order: 'asc'}]"
        :search="search">
        <template v-slot:top>
            <v-toolbar
                flat>
                <v-toolbar-title>{{ title }}</v-toolbar-title>
                <v-divider
                    class="mx-4"
                    inset
                    vertical>
                </v-divider>
                <v-spacer></v-spacer>
                <v-text-field
                    v-model="search"
                    prepend-inner-icon="mdi-magnify"
                    variant="underlined"
                    color="primary"
                    placeholder="Поиск">
                </v-text-field>
                <v-spacer></v-spacer>
                <v-btn
                    class="mb-2"
                    color="primary"
                    dark
                    @click="emit('add-item')">
                    Добавить
                    <v-icon class="ml-1" right>{{ btnIcon }}</v-icon>
                </v-btn>
                <v-dialog v-model="dialogDelete" max-width="500px">
                    <v-card>
                        <v-card-title class="text-h5">Вы уверены, что хотите удалить эту запись?</v-card-title>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue-darken-1" variant="text" @click="closeDelete">Отмена</v-btn>
                            <v-btn color="blue-darken-1" variant="text"
                                   @click="emit('delete-item-confirm', props.editedItem.id)">ОК
                            </v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon
                class="me-2"
                size="small"
                @click="emit('edit-item', item)">
                mdi-pencil
            </v-icon>
            <v-icon
                size="small"
                @click="deleteItem(item)">
                mdi-delete
            </v-icon>
        </template>
        <!--        <template v-slot:no-data>-->
        <!--            <v-btn-->
        <!--                color="primary"-->
        <!--                @click="initialize"-->
        <!--            >-->
        <!--                Reset-->
        <!--            </v-btn>-->
        <!--        </template>-->
    </v-data-table>
</template>

<style scoped>

</style>

