<script setup lang="ts">
import { MoreHorizontal } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';


const props = defineProps({
  account: { type: Object, required: true }
})

const emit = defineEmits(["edit", "delete"]); // emits to parent

function onEdit() {
  emit("edit");
}

function onDelete() {
  console.log("Delete clicked");
  emit("delete");
}

</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="w-8 h-8 p-0">
        <span class="sr-only">Open menu</span>
        <MoreHorizontal class="w-4 h-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuLabel>Actions</DropdownMenuLabel>
      <DropdownMenuSeparator />
      <DropdownMenuItem @click="onEdit">Edit</DropdownMenuItem>
      <DropdownMenuItem @click="onDelete" class="text-red-600">Delete</DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
