import { ColumnDef } from '@tanstack/vue-table'
import DropdownAction from './DataTableDropDown.vue';
import { h } from 'vue'
import { ArrowUpDown, ChevronDown } from 'lucide-vue-next'
import { Button } from '@/components/ui/button';


export const columns: ColumnDef<Prospect>[] = [
  {
    accessorKey: 'id',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['IDs', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'text-center' }, row.getValue('id')),
  },
  {
    accessorFn: row => row.company.company_name,
    id: 'company_name',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Company Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => {
      return h('div', { class: 'text-center' }, row.getValue('company_name'))
    }
  },
  {
    accessorFn: row => row.company.address,
    id: 'address',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Address', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => {
      return h('div', { class: 'text-center' }, row.getValue('address'))
    }
  },
  {
    accessorFn: row => row.company.industry,
    id: 'industry',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Industry', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => {
      return h('div', { class: 'text-center' }, row.getValue('industry'))
    }
  },
  {
    accessorKey: 'email_status_label',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Email', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'text-center' }, row.getValue('email_status_label')),
  },
  {
    accessorKey: 'phone_call_status_label',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Call Status', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'text-center' }, row.getValue('phone_call_status_label')),
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row, table }) => {
      const prospect = { ...row.original }   // clean shallow copy (removes internals)
      return h('div', { class: 'relative' }, h(DropdownAction, {
        prospect,
        onEdit: (id: number) => table.options.meta?.edit(prospect),
        onDelete: (id: number) => table.options.meta?.delete(prospect.id),
      }))
    },
  },
]
