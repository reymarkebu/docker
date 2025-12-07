<script setup lang="ts" generic="TData, TValue">

import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';

import { type BreadcrumbItem } from '@/types';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Rocket, User, CalendarIcon } from 'lucide-vue-next';

import dayjs from 'dayjs'
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod'
import { Form, Field as VeeField } from 'vee-validate'

import { ref, watch } from 'vue';
import { Head, Link, usePage, router, useForm } from '@inertiajs/vue3';

//--- Import DataTable Component ---
import { columns } from './components/columns';
import DataTable from './components/DataTable.vue';
// --- end

import { CalendarDate, DateFormatter, getLocalTimeZone, today, DateValue } from '@internationalized/date';
import { cn } from '@/lib/utils';
import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover'



import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

import {
    Field,
    FieldDescription,
    FieldError,
    FieldGroup,
    FieldLabel,
} from '@/components/ui/field'
import { Input } from '@/components/ui/input';

import {
    Select,
    SelectTrigger,
    SelectContent,
    SelectItem,
    SelectValue,
} from "@/components/ui/select"


// --- end

interface Lead {
    id: string;
    company: {
        company_name: string;
        address: string;
        industry: string;
    },
    has_existing_hmo: string;
}

interface ProposalTypes {
    id: number;
    label: string;
}

interface PhoneCallStatuses {
    id: number;
    label: string;
}

interface IslandGroups {
    id: number;
    label: string;
}

interface AccountTypes {
    prospect: "prospect";
    lead: "lead";
    account: "account";
}

interface Props {
    leads: Lead[],
    emailStatuses: EmailStatuses[],
    proposalTypes: ProposalTypes[],
    phoneCallStatuses: PhoneCallStatuses[],
    islandGroups: IslandGroups[],
    accountTypes: AccountTypes;
}

const props = defineProps<Props>();

console.log("props", props);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Leads',
        href: '/leads',
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
        account_type: '',
    },
    lead: {
        id: '',
        designation: '',
        proposal_type: '',
        phone_call_status: '',
        phone_called_at: '',
        has_existing_hmo: '',
        expiry_date: '',
        created_at: '',
    },
    expiry_date: '',

});

const open = ref(false);
const isSubmitting = ref(false);
const page = usePage();
const showFlash = ref(false);
const isDatePopoverOpen = ref(false);

const defaultPlaceholder = today(getLocalTimeZone());
const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});
// Format into YYYY-MM-DD
function formatYYYYMMDD(d) {
    if (!d) return null
    const js = d.toDate(getLocalTimeZone())
    const yyyy = js.getFullYear()
    const mm = String(js.getMonth() + 1).padStart(2, "0")
    const dd = String(js.getDate()).padStart(2, "0")
    return `${yyyy}-${mm}-${dd}`
}


const date = ref(toCalendarDate(form.expiry_date))

const formSchema = toTypedSchema(z.object({
    company_name: z.string().min(2).max(50),
    address: z.string().min(2).max(50),
    email_address1: z.string().email({ message: 'Please enter a valid email address' }).min(0, { message: 'Email is required' }),
    email_address2: z.string().email().nullable().optional(),
    email_address3: z.string().email().nullable().optional(),
    proposal_type: z.number().int(),
    phone_number1: z.string().nullable().optional(),
    phone_number2: z.string().nullable().optional(),
    phone_number3: z.string().nullable().optional(),
    phone_call_status: z.number().int(),
    company_size: z.number().nullable(),
    contact_person: z.string().min(2).max(50),
    island_group: z.number().int(),
    industry: z.string().min(2).max(50),
    account_type: z.string().min(2).max(50),
    // expiry_date: z.string().nullable(),
    has_existing_hmo: z.string().min(2).max(50),
    designation: z.string().min(2).max(50),
}))


function toCalendarDate(value) {
    if (!value) return null

    // If "2025-01-01"
    if (typeof value === "string") {
        const match = value.match(/^(\d{4})-(\d{2})-(\d{2})$/)
        if (match) {
            const [_, y, m, d] = match
            return new CalendarDate(Number(y), Number(m), Number(d))
        }
    }

    // If JS Date
    if (value instanceof Date) {
        return new CalendarDate(
            value.getFullYear(),
            value.getMonth() + 1,
            value.getDate()
        )
    }

    // Already a CalendarDate?
    try {
        if (value instanceof CalendarDate) return value
    } catch (_) { }

    return null
}

function handleEdit(lead: object) {
    form.reset(); // Reset form fields before populating

    console.log("lead edit", lead);

    Object.assign(form, {
        id: lead.id,
        company_name: lead.company.company_name,
        address: lead.company.address,
        email_address1: lead.company.email_address1,
        email_address2: lead.company.email_address2,
        email_address3: lead.company.email_address3,
        industry: lead.company.industry,
        phone_number1: lead.company.phone_number1,
        phone_number2: lead.company.phone_number2,
        phone_number3: lead.company.phone_number3,
        company_size: lead.company.company_size,
        contact_person: lead.company.contact_person,
        island_group: lead.company.island_group,
        account_type: lead.company.account_type,

        designation: lead.designation,
        proposal_type: lead.proposal_type,
        phone_call_status: lead.phone_call_status,
        phone_called_at: lead.phone_called_at,
        has_existing_hmo: lead.has_existing_hmo,
        expiry_date: lead.expiry_date,
        company_id: lead.company_id,
    });

    open.value = true;

    console.log("edit lead form", form);
}

function handleDelete(id: string) {

}


function updateLead(lead: object) {
    console.log("update lead", lead);


    isSubmitting.value = true;

    Object.assign(form, {
        company: {
            company_name: lead.company_name,
            address: lead.address,
            email_address1: lead.email_address1,
            email_address2: lead.email_address2,
            email_address3: lead.email_address3,
            industry: lead.industry,
            phone_number1: lead.phone_number1,
            phone_number2: lead.phone_number2,
            phone_number3: lead.phone_number3,
            company_size: lead.company_size,
            contact_person: lead.contact_person,
            island_group: lead.island_group,
            account_type: lead.account_type,
        },
        lead: {
            designation: lead.designation,
            proposal_type: lead.proposal_type,
            phone_call_status: lead.phone_call_status,
            has_existing_hmo: lead.has_existing_hmo,
            expiry_date: form.expiry_date,
            company_id: form.company_id
        }
    });

    console.log("update form", form);

    form.put(route('leads.update', { lead: form }), {
        onSuccess: () => {
            open.value = false;   // Close the dialog
            form.reset();         // Reset form fields

            if (showFlash.value) {
                setTimeout(() => {
                    showFlash.value = false
                }, 3000) // 3 seconds
            }
        },
        onError: (errors) => {
            console.log('errors', errors);  // Optional: log validation errors
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });

}

watch(
    () => page.props.flash?.success,
    (msg) => {
        if (msg) {
            showFlash.value = true
            setTimeout(() => {
                showFlash.value = false
            }, 3000) // auto-dismiss
        }
    },
    { immediate: true }
)

watch(date, (newDate) => {
    console.log("watch");
    form.expiry_date = newDate ? formatYYYYMMDD(newDate) : null;
});

watch(
    () => form.expiry_date,
    (val) => {
        const parsed = toCalendarDate(val)
        if (
            !date.value ||
            !parsed ||
            parsed.year !== date.value.year ||
            parsed.month !== date.value.month ||
            parsed.day !== date.value.day
        ) {
            date.value = parsed
        }
    }
)

</script>

<template>

    <Head title="Leads" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-3">


            <Transition enter-from="opacity-0 translate-y-1" enter-to="opacity-100 translate-y-0"
                enter-active-class="transition duration-300" leave-from="opacity-100 translate-y-0"
                leave-to="opacity-0 translate-y-1" leave-active-class="transition duration-300">
                <div v-if="showFlash" class="alert mb4">
                    <Alert class="bg-blue-50 border-blue-400 mb-4" role="alert">
                        <Rocket class="h-4 w-4" />
                        <AlertTitle>Notification</AlertTitle>
                        <AlertDescription>
                            {{ page.props.flash.success }}
                        </AlertDescription>
                    </Alert>
                </div>
            </Transition>

            <div class="container py-8 mx-auto">
                <Form v-slot="{ handleSubmit }" as="" :validation-schema="formSchema">
                    <Dialog v-model:open="open">
                        <DialogContent class="w-full max-w-5xl max-h-[80vh] overflow-y-auto">
                            <DialogHeader>
                                <DialogTitle>Update Lead</DialogTitle>
                                <div class="grid grid-cols-4 gap-4 h-full">
                                    <div class="p-3 rounded">
                                        <FieldGroup>
                                            <DialogDescription>
                                                <div> Lead ID: {{ form.id }} </div>
                                                <div>Lead Created: {{ dayjs(form.created_at).format('YYYY-MM-DD') }}
                                                </div>
                                                <div>Last Phone Call: {{ form.phone_called_at }}</div>
                                            </DialogDescription>
                                        </FieldGroup>
                                    </div>
                                    <div class="p-3 rounded"></div>
                                    <div class="p-3 rounded"></div>
                                    <div class="p-3 rounded">
                                        <FieldGroup>
                                            <VeeField name="account_type" v-model="form.account_type"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Convert Lead</FieldLabel>
                                                    <Select id="account_type" v-model="form.account_type">
                                                        <SelectTrigger class="w-full">
                                                            <SelectValue />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem v-for="(value, key) in props.accountTypes"
                                                                :key="key" :value="key">{{ value }}</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.account_type']"
                                                        :errors="[{ message: form.errors['company.account_type'] }]" />
                                                </Field>
                                            </VeeField>
                                        </FieldGroup>
                                    </div>
                                </div>

                            </DialogHeader>
                            <form id="dialogForm" @submit="handleSubmit($event, updateLead)">
                                <div class="grid grid-cols-4 gap-4 h-full overflow-y-auto">
                                    <div class="bg-muted/30 p-4 rounded">
                                        <FieldGroup>
                                            <VeeField name="company_name" v-model="form.company_name"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Company Name</FieldLabel>
                                                    <Input type="text" v-model="form.company_name" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <!-- Inertia backend validation errors -->
                                                    <FieldError v-if="form.errors['company.company_name']"
                                                        :errors="[{ message: form.errors['company.company_name'] }]" />
                                                </Field>
                                            </VeeField>
                                            <VeeField name="address" v-model="form.address" v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Address</FieldLabel>
                                                    <Input type="text" v-model="form.address" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.address']"
                                                        :errors="[{ message: form.errors['company.address'] }]" />
                                                </Field>

                                            </VeeField>
                                            <VeeField name="email_address1" v-model="form.email_address1"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Email Address 1</FieldLabel>
                                                    <Input type="text" v-model="form.email_address1" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.email_address1']"
                                                        :errors="[{ message: form.errors['company.email_address1'] }]" />
                                                </Field>
                                            </VeeField>
                                            <VeeField name="email_address2" v-model="form.email_address2"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Email Address 2</FieldLabel>
                                                    <Input type="text" v-model="form.email_address2" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.email_address2']"
                                                        :errors="[{ message: form.errors['company.email_address2'] }]" />
                                                </Field>
                                            </VeeField>
                                            <VeeField name="email_address3" v-model="form.email_address3"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Email Address 3</FieldLabel>
                                                    <Input type="text" v-model="form.email_address3" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.email_address3']"
                                                        :errors="[{ message: form.errors['company.email_address3'] }]" />
                                                </Field>
                                            </VeeField>
                                        </FieldGroup>
                                    </div>
                                    <div class="bg-muted/30 p-4 rounded">
                                        <FieldGroup>
                                            <VeeField name="phone_number1" v-model="form.phone_number1"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Phone Number 1</FieldLabel>
                                                    <Input type="text" v-model="form.phone_number1" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.phone_number1']"
                                                        :errors="[{ message: form.errors['company.phone_number1'] }]" />
                                                </Field>
                                            </VeeField>

                                            <VeeField name="phone_number2" v-model="form.phone_number2"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Phone Number 2</FieldLabel>
                                                    <Input type="text" v-model="form.phone_number2" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.phone_number2']"
                                                        :errors="[{ message: form.errors['company.phone_number2'] }]" />
                                                </Field>
                                            </VeeField>
                                            <VeeField name="phone_number3" v-model="form.phone_number3"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Phone Number 3</FieldLabel>
                                                    <Input type="text" v-model="form.phone_number3" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.phone_number3']"
                                                        :errors="[{ message: form.errors['company.phone_number3'] }]" />
                                                </Field>
                                            </VeeField>
                                            <VeeField name="contact_person" v-model="form.contact_person"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Contact Person</FieldLabel>
                                                    <Input v-model="form.contact_person" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.contact_person']"
                                                        :errors="[{ message: form.errors['company.contact_person'] }]" />
                                                </Field>
                                            </VeeField>
                                            <VeeField name="designation" v-model="form.designation" v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Designation</FieldLabel>
                                                    <Input v-model="form.designation" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['lead.designation']"
                                                        :errors="[{ message: form.errors['lead.designation'] }]" />
                                                </Field>
                                            </VeeField>
                                        </FieldGroup>
                                    </div>
                                    <div class="bg-muted/30 p-4 rounded">
                                        <FieldGroup>
                                            <VeeField name="has_existing_hmo" v-model="form.has_existing_hmo"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Has Existing HMO</FieldLabel>
                                                    <Select id="has_existing_hmo" v-model="form.has_existing_hmo">
                                                        <SelectTrigger class="w-full">
                                                            <SelectValue placeholder="" />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem value="yes"> Yes </SelectItem>
                                                            <SelectItem value="no"> No </SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.island_group']"
                                                        :errors="[{ message: form.errors['company.island_group'] }]" />
                                                </Field>
                                            </VeeField>
                                            <Field v-if="form.has_existing_hmo == 'yes'">
                                                <FieldLabel>Expiry Date</FieldLabel>
                                                <Popover v-model:open="isDatePopoverOpen">
                                                    <PopoverTrigger as-child>
                                                        <Button variant="outline"
                                                            :class="cn('w-[200px] justify-start text-left font-normal', !date && 'text-muted-foreground')">
                                                            <CalendarIcon />
                                                            {{ date ? formatYYYYMMDD(date) : "Pick a date" }}
                                                        </Button>
                                                    </PopoverTrigger>

                                                    <PopoverContent class="w-auto p-0" align="start">
                                                        <Calendar v-model="date"
                                                            :default-placeholder="defaultPlaceholder"
                                                            layout="month-and-year" initial-focus
                                                            @update:model-value="isDatePopoverOpen = false" />
                                                    </PopoverContent>
                                                </Popover>
                                            </Field>
                                            <VeeField name="island_group" v-model="form.island_group"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Island Group</FieldLabel>
                                                    <Select id="island_group" v-model="form.island_group">
                                                        <SelectTrigger class="w-full">
                                                            <SelectValue placeholder="Island Group" />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem v-for="item in props.islandGroups"
                                                                :key="item.id" :value="item.id">{{
                                                                    item.label }}</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.island_group']"
                                                        :errors="[{ message: form.errors['company.island_group'] }]" />
                                                </Field>
                                            </VeeField>
                                            <VeeField name="company_size" v-model="form.company_size"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Company Size</FieldLabel>
                                                    <Input type="number" v-model="form.company_size" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.company_size']"
                                                        :errors="[{ message: form.errors['company.company_size'] }]" />
                                                </Field>
                                            </VeeField>
                                        </FieldGroup>
                                    </div>
                                    <div class="bg-muted/30 p-4 rounded">
                                        <FieldGroup>
                                            <VeeField name="proposal_type" v-model="form.proposal_type"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Proposal Type</FieldLabel>
                                                    <Select id="proposal_type" v-model="form.proposal_type">
                                                        <SelectTrigger class="w-full">
                                                            <SelectValue placeholder="Proposal Type" />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem v-for="item in props.proposalTypes"
                                                                :key="item.id" :value="item.id">{{
                                                                    item.label }}</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['lead.proposal_type']"
                                                        :errors="[{ message: form.errors['lead.proposal_type'] }]" />
                                                </Field>
                                            </VeeField>
                                            <VeeField name="phone_call_status" v-model="form.phone_call_status"
                                                v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Phone Call Status</FieldLabel>
                                                    <Select id="phone_call_status" v-model="form.phone_call_status">
                                                        <SelectTrigger class="w-full">
                                                            <SelectValue placeholder="Phone Call Status" />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem v-for="item in props.phoneCallStatuses"
                                                                :key="item.id" :value="item.id">{{
                                                                    item.label }}</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['lead.phone_call_status']"
                                                        :errors="[{ message: form.errors['lead.phone_call_status'] }]" />
                                                </Field>
                                            </VeeField>
                                            <VeeField name="industry" v-model="form.industry" v-slot="{ errors }">
                                                <Field>
                                                    <FieldLabel>Industry</FieldLabel>
                                                    <Input type="text" v-model="form.industry" />
                                                    <FieldError v-if="errors.length"
                                                        :errors="errors.map(e => ({ message: e }))" />
                                                    <FieldError v-if="form.errors['company.industry']"
                                                        :errors="[{ message: form.errors['company.industry'] }]" />
                                                </Field>
                                            </VeeField>
                                            <Button type="submit" form="dialogForm" :disabled="isSubmitting">
                                                {{ isSubmitting ? 'Updating...' : 'Update Lead' }}
                                            </Button>
                                        </FieldGroup>
                                    </div>
                                </div>
                            </form>
                        </DialogContent>
                    </Dialog>
                </Form>
            </div>
            <div class="container py-8 mx-auto">
                <DataTable :columns="columns" :data="props.leads" :meta="{ edit: handleEdit, delete: handleDelete }" />
            </div>
        </div>
    </AppLayout>
</template>