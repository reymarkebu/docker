<script setup>
import { computed } from "vue"
import { Button } from "@/components/ui/button"
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"

const props = defineProps({
    table: { type: Object, required: true },
    showSelectedCount: { type: Boolean, default: false },
})

// Current pagination state
const pageIndex = computed(() => props.table.getState().pagination.pageIndex)
const pageSize = computed(() => props.table.getState().pagination.pageSize)
const pageCount = computed(() => props.table.getPageCount())

// Row selection count
const selectedCount = computed(
    () => props.table.getSelectedRowModel().flatRows.length
)

// Compute visible page numbers (max 5 buttons)
const visiblePages = computed(() => {
    const total = pageCount.value
    const current = pageIndex.value
    const maxButtons = 5

    if (total <= maxButtons) {
        return Array.from({ length: total }, (_, i) => i)
    }

    const half = Math.floor(maxButtons / 2)
    let start = Math.max(0, current - half)
    let end = Math.min(total - 1, current + half)

    // Normalize window
    if (end - start + 1 < maxButtons) {
        if (start === 0) end = start + maxButtons - 1
        else if (end === total - 1) start = end - maxButtons + 1
    }

    return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})
</script>

<template>
    <div class="flex items-center justify-between w-full py-2">

        <!-- Selection Count -->
        <!-- <div class="text-sm text-muted-foreground" v-if="showSelectedCount">
            {{ selectedCount }} selected
        </div> -->
        
        <!-- Page Size Selector -->
        <div class="flex items-center gap-2">
            <span class="text-sm">Rows per page</span>

            <Select :model-value="String(pageSize)" @update:model-value="(v) => props.table.setPageSize(Number(v))">
                <SelectTrigger class="w-20">
                    <SelectValue />
                </SelectTrigger>

                <SelectContent>
                    <SelectGroup>
                        <SelectItem v-for="s in [5, 10, 20, 50, 100]" :key="s" :value="String(s)">
                            {{ s }}
                        </SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>


        <!-- Pagination Controls -->
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <!-- Prev -->
                <Button variant="outline" size="sm" :disabled="!props.table.getCanPreviousPage()"
                    @click="props.table.previousPage()">
                    Prev
                </Button>

                <!-- Page Number Buttons -->
                <div class="flex items-center gap-1">
                    <Button v-for="p in visiblePages" :key="p" size="sm"
                        :variant="p === pageIndex ? 'default' : 'outline'" @click="props.table.setPageIndex(p)">
                        {{ p + 1 }}
                    </Button>
                </div>

                <!-- Next -->
                <Button variant="outline" size="sm" :disabled="!props.table.getCanNextPage()"
                    @click="props.table.nextPage()">
                    Next
                </Button>

            </div>

        </div>
    </div>
</template>
