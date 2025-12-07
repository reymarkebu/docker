<script setup lang="ts" generic="TData, TValue">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

import { Rocket, CalendarIcon } from 'lucide-vue-next';
import { toTypedSchema } from '@vee-validate/zod'
import { Form, Field as VeeField } from 'vee-validate'
import { z } from 'zod'
import { Button } from '@/components/ui/button'

import { CalendarDate, DateFormatter, getLocalTimeZone, today, DateValue } from '@internationalized/date';
import { cn } from '@/lib/utils';
import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover'

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
} from "@/components/ui/select";


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Edit Account',
        href: '/accounts',
    },
];

interface Account {
    id: string;
    plan_type: string;
    contract_start_date: string;
    contract_end_date: string;
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

interface PlanTypes {
    id: number;
    label: string;
}

interface Props {
    account: Account;
    islandGroups: IslandGroups[],
    accountTypes: AccountTypes;
    planTypes: PlanTypes;
}
const props = defineProps<Props>();
console.log("props", props);


// Object.assign(form, {
//     company: {
//         company_name: account.company_name,
//         address: account.address,
//         email_address1: account.email_address1,
//         email_address2: account.email_address2,
//         email_address3: account.email_address3,
//         industry: account.industry,
//         phone_number1: account.phone_number1,
//         phone_number2: account.phone_number2,
//         phone_number3: account.phone_number3,
//         company_size: account.company_size,
//         contact_person: account.contact_person,
//         island_group: account.island_group,
//         account_type: account.account_type,
//     },
//     account: {
//         designation: account.designation,
//         plan_type: account.plan_type,
//         contract_start_date: form.contract_start_date,
//         contract_end_date: form.contract_end_date,
//         company_id: form.company_id
//     }
// });
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
    account: {
        id: '',
        designation: '',
        plan_type: '',
        contract_start_date: '',
        contract_end_date: '',
        company_id: '',
    },
});

const isSubmitting = ref(false);
const showFlash = ref(false);
const page = usePage();

const isStartDatePopoverOpen = ref(false);
const isEndDatePopoverOpen = ref(false);
const defaultPlaceholder = today(getLocalTimeZone());
const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

Object.assign(form, {
    id: props.account.id,
    company_name: props.account.company.company_name,
    address: props.account.company.address,
    email_address1: props.account.company.email_address1,
    email_address2: props.account.company.email_address2,
    email_address3: props.account.company.email_address3,
    industry: props.account.company.industry,
    phone_number1: props.account.company.phone_number1,
    phone_number2: props.account.company.phone_number2,
    phone_number3: props.account.company.phone_number3,
    company_size: props.account.company.company_size,
    contact_person: props.account.company.contact_person,
    island_group: props.account.company.island_group,
    account_type: props.account.company.account_type,
    designation: props.account.designation,
    plan_type: props.account.plan_type,
    contract_start_date: props.account.contract_start_date,
    contract_end_date: props.account.contract_end_date,
    company_id: props.account.company_id,
});

const startDate = ref(toCalendarDate(form.contract_start_date));
const endDate = ref(toCalendarDate(form.contract_end_date));

const formSchema = toTypedSchema(z.object({
    company_name: z.string().min(2).max(50),
    address: z.string().min(2).max(50),
    email_address1: z.string().email({ message: 'Please enter a valid email address' }).min(0, { message: 'Email is required' }),
    email_address2: z.string().email().nullable().optional(),
    email_address3: z.string().email().nullable().optional(),
    phone_number1: z.string().nullable().optional(),
    phone_number2: z.string().nullable().optional(),
    phone_number3: z.string().nullable().optional(),
    company_size: z.number().nullable(),
    contact_person: z.string().min(2).max(50),
    island_group: z.number().int(),
    industry: z.string().min(2).max(50),
    account_type: z.string().min(2).max(50),
    plan_type: z.number().int(),
    designation: z.string().min(2).max(50),
    // contract_start_date: z.string().min(2).max(50),
    // contract_end_date: z.string().min(2).max(50),
}))

function formatYYYYMMDD(d) {
    if (!d) return null
    const js = d.toDate(getLocalTimeZone())
    const yyyy = js.getFullYear()
    const mm = String(js.getMonth() + 1).padStart(2, "0")
    const dd = String(js.getDate()).padStart(2, "0")
    return `${yyyy}-${mm}-${dd}`
}

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

function updateAccount(account: object) {
    console.log("update", account);

    isSubmitting.value = true;

    Object.assign(form, {
        company: {
            company_name: account.company_name,
            address: account.address,
            email_address1: account.email_address1,
            email_address2: account.email_address2,
            email_address3: account.email_address3,
            industry: account.industry,
            phone_number1: account.phone_number1,
            phone_number2: account.phone_number2,
            phone_number3: account.phone_number3,
            company_size: account.company_size,
            contact_person: account.contact_person,
            island_group: account.island_group,
            account_type: account.account_type,
        },
        account: {
            id: form.id,
            designation: account.designation,
            plan_type: account.plan_type,
            contract_start_date: form.contract_start_date,
            contract_end_date: form.contract_end_date,
            company_id: form.company_id
        }
    });

    console.log("update form", form);
    console.log("update form", account);

    form.put(route('accounts.update', { account: form.id }), {
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
};

watch(startDate, (newDate) => {
    console.log("watch");
    form.contract_start_date = newDate ? formatYYYYMMDD(newDate) : null;
});

watch(endDate, (newDate) => {
    console.log("watch end");
    form.contract_end_date = newDate ? formatYYYYMMDD(newDate) : null;
});

watch(
    () => form.contract_start_date,
    (val) => {
        console.log("watch 2");
        const parsed = toCalendarDate(val)
        if (
            !startDate.value ||
            !parsed ||
            parsed.year !== startDate.value.year ||
            parsed.month !== startDate.value.month ||
            parsed.day !== startDate.value.day
        ) {
            startDate.value = parsed
        }
    }
)

watch(
    () => form.contract_end_date,
    (val) => {
        console.log("watch end");
        const parsed = toCalendarDate(val)
        if (
            !endDate.value ||
            !parsed ||
            parsed.year !== endDate.value.year ||
            parsed.month !== endDate.value.month ||
            parsed.day !== endDate.value.day
        ) {
            endDate.value = parsed
        }
    }
)

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

</script>
<template>

    <Head title="Edit Account" />

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
                    <form id="dialogForm" @submit="handleSubmit($event, updateAccount)">
                        <div class="grid grid-cols-4 gap-4 h-full overflow-y-auto">
                            <div class="bg-muted/30 p-4 rounded">
                                <FieldGroup>
                                    <VeeField name="company_name" v-model="form.company_name" v-slot="{ errors }">
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
                                    <VeeField name="email_address1" v-model="form.email_address1" v-slot="{ errors }">
                                        <Field>
                                            <FieldLabel>Email Address 1</FieldLabel>
                                            <Input type="text" v-model="form.email_address1" />
                                            <FieldError v-if="errors.length"
                                                :errors="errors.map(e => ({ message: e }))" />
                                            <FieldError v-if="form.errors['company.email_address1']"
                                                :errors="[{ message: form.errors['company.email_address1'] }]" />
                                        </Field>
                                    </VeeField>
                                    <VeeField name="email_address2" v-model="form.email_address2" v-slot="{ errors }">
                                        <Field>
                                            <FieldLabel>Email Address 2</FieldLabel>
                                            <Input type="text" v-model="form.email_address2" />
                                            <FieldError v-if="errors.length"
                                                :errors="errors.map(e => ({ message: e }))" />
                                            <FieldError v-if="form.errors['company.email_address2']"
                                                :errors="[{ message: form.errors['company.email_address2'] }]" />
                                        </Field>
                                    </VeeField>
                                    <VeeField name="email_address3" v-model="form.email_address3" v-slot="{ errors }">
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
                                    <VeeField name="phone_number1" v-model="form.phone_number1" v-slot="{ errors }">
                                        <Field>
                                            <FieldLabel>Phone Number 1</FieldLabel>
                                            <Input type="text" v-model="form.phone_number1" />
                                            <FieldError v-if="errors.length"
                                                :errors="errors.map(e => ({ message: e }))" />
                                            <FieldError v-if="form.errors['company.phone_number1']"
                                                :errors="[{ message: form.errors['company.phone_number1'] }]" />
                                        </Field>
                                    </VeeField>

                                    <VeeField name="phone_number2" v-model="form.phone_number2" v-slot="{ errors }">
                                        <Field>
                                            <FieldLabel>Phone Number 2</FieldLabel>
                                            <Input type="text" v-model="form.phone_number2" />
                                            <FieldError v-if="errors.length"
                                                :errors="errors.map(e => ({ message: e }))" />
                                            <FieldError v-if="form.errors['company.phone_number2']"
                                                :errors="[{ message: form.errors['company.phone_number2'] }]" />
                                        </Field>
                                    </VeeField>
                                    <VeeField name="phone_number3" v-model="form.phone_number3" v-slot="{ errors }">
                                        <Field>
                                            <FieldLabel>Phone Number 3</FieldLabel>
                                            <Input type="text" v-model="form.phone_number3" />
                                            <FieldError v-if="errors.length"
                                                :errors="errors.map(e => ({ message: e }))" />
                                            <FieldError v-if="form.errors['company.phone_number3']"
                                                :errors="[{ message: form.errors['company.phone_number3'] }]" />
                                        </Field>
                                    </VeeField>
                                    <VeeField name="contact_person" v-model="form.contact_person" v-slot="{ errors }">
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
                                            <FieldError v-if="form.errors['account.designation']"
                                                :errors="[{ message: form.errors['account.designation'] }]" />
                                        </Field>
                                    </VeeField>
                                </FieldGroup>
                            </div>
                            <div class="bg-muted/30 p-4 rounded">
                                <FieldGroup>

                                    <VeeField name="island_group" v-model="form.island_group" v-slot="{ errors }">
                                        <Field>
                                            <FieldLabel>Island Group</FieldLabel>
                                            <Select id="island_group" v-model="form.island_group">
                                                <SelectTrigger class="w-full">
                                                    <SelectValue placeholder="Island Group" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="item in props.islandGroups" :key="item.id"
                                                        :value="item.id">{{
                                                            item.label }}</SelectItem>
                                                </SelectContent>
                                            </Select>
                                            <FieldError v-if="errors.length"
                                                :errors="errors.map(e => ({ message: e }))" />
                                            <FieldError v-if="form.errors['company.island_group']"
                                                :errors="[{ message: form.errors['company.island_group'] }]" />
                                        </Field>
                                    </VeeField>
                                    <VeeField name="company_size" v-model="form.company_size" v-slot="{ errors }">
                                        <Field>
                                            <FieldLabel>Company Size</FieldLabel>
                                            <Input type="number" v-model="form.company_size" />
                                            <FieldError v-if="errors.length"
                                                :errors="errors.map(e => ({ message: e }))" />
                                            <FieldError v-if="form.errors['company.company_size']"
                                                :errors="[{ message: form.errors['company.company_size'] }]" />
                                        </Field>
                                    </VeeField>
                                    <VeeField name="plan_type" v-model="form.plan_type" v-slot="{ errors }">
                                        <Field>
                                            <FieldLabel>Plan Type</FieldLabel>
                                            <Select id="plan_type" v-model="form.plan_type">
                                                <SelectTrigger class="w-full">
                                                    <SelectValue placeholder="Plan Type" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="item in planTypes" :key="item.id"
                                                        :value="item.id">{{
                                                            item.label }}</SelectItem>
                                                </SelectContent>
                                            </Select>
                                            <FieldError v-if="errors.length"
                                                :errors="errors.map(e => ({ message: e }))" />
                                            <FieldError v-if="form.errors['account.plan_type']"
                                                :errors="[{ message: form.errors['account.plan_type'] }]" />
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
                                </FieldGroup>
                            </div>
                            <div class="bg-muted/30 p-4 rounded">
                                <FieldGroup>
                                    <VeeField name="account_type" v-model="form.account_type" v-slot="{ errors }">
                                        <Field>
                                            <FieldLabel>Convert Lead</FieldLabel>
                                            <Select id="account_type" v-model="form.account_type">
                                                <SelectTrigger class="w-full">
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="(value, key) in props.accountTypes" :key="key"
                                                        :value="key">{{ value }}</SelectItem>
                                                </SelectContent>
                                            </Select>
                                            <FieldError v-if="errors.length"
                                                :errors="errors.map(e => ({ message: e }))" />
                                            <FieldError v-if="form.errors['company.account_type']"
                                                :errors="[{ message: form.errors['company.account_type'] }]" />
                                        </Field>
                                    </VeeField>
                                    <Field>
                                        <FieldLabel>Contract Start Date</FieldLabel>
                                        <Popover v-model:open="isStartDatePopoverOpen">
                                            <PopoverTrigger as-child>
                                                <Button variant="outline"
                                                    :class="cn('w-[200px] justify-start text-left font-normal', !startDate && 'text-muted-foreground')">
                                                    <CalendarIcon />
                                                    {{ startDate ? formatYYYYMMDD(startDate) : "Pick a date" }}
                                                </Button>
                                            </PopoverTrigger>

                                            <PopoverContent class="w-auto p-0" align="start">
                                                <Calendar v-model="startDate" :default-placeholder="defaultPlaceholder"
                                                    layout="month-and-year" initial-focus
                                                    @update:model-value="isStartDatePopoverOpen = false" />
                                            </PopoverContent>
                                        </Popover>
                                    </Field>
                                    <Field>
                                        <FieldLabel>Contract End Date</FieldLabel>
                                        <Popover v-model:open="isEndDatePopoverOpen">
                                            <PopoverTrigger as-child>
                                                <Button variant="outline"
                                                    :class="cn('w-[200px] justify-start text-left font-normal', !endDate && 'text-muted-foreground')">
                                                    <CalendarIcon />
                                                    {{ endDate ? formatYYYYMMDD(endDate) : "Pick a date" }}
                                                </Button>
                                            </PopoverTrigger>

                                            <PopoverContent class="w-auto p-0" align="start">
                                                <Calendar v-model="endDate" :default-placeholder="defaultPlaceholder"
                                                    layout="month-and-year" initial-focus
                                                    @update:model-value="isEndDatePopoverOpen = false" />
                                            </PopoverContent>
                                        </Popover>
                                    </Field>

                                    <Button type="submit" form="dialogForm" :disabled="isSubmitting">
                                        {{ isSubmitting ? 'Updating...' : 'Update Account' }}
                                    </Button>
                                </FieldGroup>
                            </div>
                        </div>
                    </form>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>