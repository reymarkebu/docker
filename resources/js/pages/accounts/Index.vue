<script setup lang="ts" generic="TData, TValue">

import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

//--- Import DataTable Component ---
import { columns } from './components/columns';
import DataTable from './components/DataTable.vue';

interface Account {
    id: string;
    company: {
        company_name: string;
        address: string;
        industry: string;
    },
    plan_type: string;
    contract_start_date: string;
    contract_end_date: string;
}

interface Props {
    accounts: Account[]
}

const props = defineProps<Props>();

console.log("props", props);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Accounts',
        href: '/accounts',
    },
];

const form = useForm({
    company: {
        company_name: '',
        address: '',
        email_address1: '',
        email_address2: '',
        email_address3: '',
        industry: '',
        phone_number1: '',
        phone_number2: '',
        phone_number3: '',
        company_size: '',
        contact_person: '',
        island_group: '',
    },
    account: {
        id: '',
        designation: '',
        plan_type: '',
        contract_start_date: '',
        contract_end_date: '',
    },
});

function handleEdit(id: string) {
    console.log('handle edit', id);
    // form.get(route('accounts.edit', {id: id }));
    router.get(route('accounts.edit', id));
}

function handleDelete() {

}

</script>
<template>
    <Head title="Accounts" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-3">
             <div class="container py-8 mx-auto">
                <DataTable :columns="columns" :data="props.accounts" :meta="{ edit: handleEdit, delete: handleDelete }" />
            </div>
        </div>
    </AppLayout>
</template>