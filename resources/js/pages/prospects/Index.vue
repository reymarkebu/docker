<script setup lang="ts" generic="TData, TValue">

import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, router, useForm } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Rocket, User } from 'lucide-vue-next';

import { h, ref, onMounted, watch } from "vue";

//--- Import DataTable Component ---
import { columns } from './components/columns'
import DataTable from './components/DataTable.vue'

// --- Dialog Form Imports ---
import { Textarea } from "@/components/ui/textarea"
import { toTypedSchema } from '@vee-validate/zod'
import { Form, Field as VeeField } from 'vee-validate'

import * as z from 'zod';
import dayjs from 'dayjs'

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
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'

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



interface Prospect {
  id: string;
  company: {
    company_name: string;
    address: string;
    industry: string;
  }
  email_address1: string;
  phone_call_status: string;
}

interface EmailStatuses {
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
  prospects: Prospect[],
  emailStatuses: EmailStatuses[],
  phoneCallStatuses: PhoneCallStatuses[],
  islandGroups: IslandGroups[],
  accountTypes: AccountTypes;
}
const props = defineProps<Props>();

console.log("props", props);

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Prospects',
    href: '/prospects',
  },
];

const page = usePage();
const showFlash = ref(false);


// start of dialog form logic-----------------------

const open = ref(false);
const openEdit = ref(false);
const alertDelete = ref(false);


const form = useForm({
  company: {
    id: '',
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
    notes: '',
    created_at: '',
    account_type: '',
  },
  prospect: {
    email_status: '',
    email_sent_at: '',
    phone_call_status: '',
    phone_called_at: '',
  }
});


const formSchema = toTypedSchema(z.object({
  company_name: z.string().min(2).max(50),
  address: z.string().min(2).max(50),
  email_address1: z.string().email({ message: 'Please enter a valid email address' }).min(0, { message: 'Email is required' }),
  email_address2: z.string().email().nullable().optional(),
  email_address3: z.string().email().nullable().optional(),
  industry: z.string().min(2).max(50),
  phone_number1: z.string().nullable().optional(),
  phone_number2: z.string().nullable().optional(),
  phone_number3: z.string().nullable().optional(),
  company_size: z.number().int(),
  contact_person: z.string().min(2).max(50),
  island_group: z.number().int(),
  notes: z.string().min(2).max(500),
}))

const formSchemaEdit = toTypedSchema(z.object({
  company_name: z.string().min(2).max(50),
  address: z.string().min(2).max(50),
  email_address1: z.string().email({ message: 'Please enter a valid email address' }).min(0, { message: 'Email is required' }),
  email_address2: z.string().email().nullable().optional(),
  email_address3: z.string().email().nullable().optional(),
  email_status: z.number().int(),
  industry: z.string().min(2).max(50),
  account_type: z.string().min(2).max(50),
  phone_number1: z.string().nullable().optional(),
  phone_number2: z.string().nullable().optional(),
  phone_number3: z.string().nullable().optional(),
  phone_call_status: z.number().int(),
  company_size: z.number().nullable(),
  contact_person: z.string().min(2).max(50),
  island_group: z.number().int(),
  notes: z.string().min(2).max(500),
}))

const isSubmitting = ref(false);


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
  { immediate: true } // show immediately on first page load if flash exists
)

// --- data table logic here ---


function createProspect(values: any) {

  console.log("create values", values);
  isSubmitting.value = true;
  Object.assign(form, {
    company: {
      company_name: values.company_name,
      address: values.address,
      email_address1: values.email_address1,
      email_address2: values.email_address2,
      email_address3: values.email_address3,
      industry: values.industry,
      phone_number1: values.phone_number1,
      phone_number2: values.phone_number2,
      phone_number3: values.phone_number3,
      company_size: values.company_size,
      contact_person: values.contact_person,
      island_group: values.island_group,
    },
    prospect: {
      notes: values.notes,
    }
  });


  console.log("create form", form);

  form.post(route('prospects.store'), {
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
      console.log('errors', form.errors);  // Optional: log validation errors
      isSubmitting.value = false;
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
}

function handleEdit(prospect: object) {
  form.reset(); // Reset form fields before populating

  console.log("editForm", prospect);

  Object.assign(form, {
    id: prospect.id,
    account_type: prospect.company.account_type,
    company_name: prospect.company.company_name,
    address: prospect.company.address,
    email_address1: prospect.company.email_address1,
    email_address2: prospect.company.email_address2,
    email_address3: prospect.company.email_address3,
    email_status: prospect.email_status,
    email_sent_at: prospect.email_sent_at,
    industry: prospect.company.industry,
    phone_number1: prospect.company.phone_number1,
    phone_number2: prospect.company.phone_number2,
    phone_number3: prospect.company.phone_number3,
    company_size: prospect.company.company_size,
    phone_call_status: prospect.phone_call_status,
    phone_called_at: prospect.phone_called_at,
    contact_person: prospect.company.contact_person,
    island_group: prospect.company.island_group,
    notes: prospect.notes,
    created_at: prospect.created_at,
    company_id: prospect.company_id,
  });

  openEdit.value = true;

}

function updateProspect(prospect: object) {

  console.log("update Prospect", prospect);

  isSubmitting.value = true;
  Object.assign(form, {
    company: {
      company_name: prospect.company_name,
      address: prospect.address,
      email_address1: prospect.email_address1,
      email_address2: prospect.email_address2,
      email_address3: prospect.email_address3,
      industry: prospect.industry,
      phone_number1: prospect.phone_number1,
      phone_number2: prospect.phone_number2,
      phone_number3: prospect.phone_number3,
      company_size: prospect.company_size,
      contact_person: prospect.contact_person,
      island_group: prospect.island_group,
      account_type: prospect.account_type,
    }
    ,
    prospect: {
      email_status: prospect.email_status,
      phone_call_status: prospect.phone_call_status,
      notes: prospect.notes,
      company_id: form.company_id,
    }
  });

  console.log("update form", form);

  form.put(route('prospects.update', { prospect: form }), {
    onSuccess: () => {
      openEdit.value = false;   // Close the dialog
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

function handleDelete(id: string) {

  console.log("delete", id);
  form.reset(); // Reset form fields before populating
  form.id = id.toString();

  alertDelete.value = true;
}

function DeleteProspect(id: string) {

  router.delete(route('prospects.destroy', { id }));
  alertDelete.value = false;
  form.reset(); // Reset form fields before populating
}
// --- end of data table logic ---
</script>

<template>

  <Head title="Prospects" />

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
            <DialogTrigger as-child>
              <Button @click="open = true" class="ml-auto">
                Create Prospect
              </Button>
            </DialogTrigger>
            <DialogContent class="w-full max-w-4xl">
              <DialogHeader>
                <DialogTitle>Create Prospect</DialogTitle>
                <DialogDescription></DialogDescription>
              </DialogHeader>

              <form id="dialogForm" @submit="handleSubmit($event, createProspect)">
                <div class="grid grid-cols-3 gap-4 h-full overflow-y-auto">
                  <div class="bg-muted/30 p-4 rounded">
                    <FieldGroup>
                      <VeeField v-slot="{ componentField, errors }" name="company_name">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="company_name">
                            Company Name
                          </FieldLabel>
                          <Input id="company_name" type="text" placeholder="Company Nanme" v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <!-- Inertia backend validation errors -->
                          <FieldError v-if="form.errors['company.company_name']"
                            :errors="[{ message: form.errors['company.company_name'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField v-slot="{ componentField, errors }" name="address">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="address">
                            Address
                          </FieldLabel>
                          <Input id="address" type="text" placeholder="Address" v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.address']"
                            :errors="[{ message: form.errors['company.address'] }]" />

                        </Field>
                      </VeeField>
                      <VeeField v-slot="{ componentField, errors }" name="email_address1">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="email_address1">
                            Email address 1
                          </FieldLabel>
                          <Input id="email_address1" type="text" placeholder="Email Address 1"
                            v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.email_address1']"
                            :errors="[{ message: form.errors['company.email_address1'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField v-slot="{ componentField, errors }" name="email_address2">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="email_address2">
                            Email address 2
                          </FieldLabel>
                          <Input id="email_address2" type="text" placeholder="Email Address 2"
                            v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.email_address2']"
                            :errors="[{ message: form.errors['company.email_address2'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField v-slot="{ componentField, errors }" name="email_address3">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="email_address3">
                            Email address 3
                          </FieldLabel>
                          <Input id="email_address3" type="text" placeholder="Email Address 3"
                            v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.email_address3']"
                            :errors="[{ message: form.errors['company.email_address3'] }]" />
                        </Field>
                      </VeeField>
                    </FieldGroup>
                  </div>
                  <div class="bg-muted/30 p-4 rounded">
                    <FieldGroup>
                      <VeeField v-slot="{ componentField, errors }" name="industry">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="industry">
                            Industry
                          </FieldLabel>
                          <Input id="industry" type="text" placeholder="Industry" v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.industry']"
                            :errors="[{ message: form.errors['company.industry'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField v-slot="{ componentField, errors }" name="phone_number1">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="phone_number1">
                            Phone Number 1
                          </FieldLabel>
                          <Input id="phone_number1" type="text" placeholder="Phone Number 1" v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.phone_number1']"
                            :errors="[{ message: form.errors['company.phone_number1'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField v-slot="{ componentField, errors }" name="phone_number2">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="phone_number2">
                            Phone Number 2
                          </FieldLabel>
                          <Input id="phone_number2" type="text" placeholder="Phone Number 2" v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.phone_number2']"
                            :errors="[{ message: form.errors['company.phone_number2'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField v-slot="{ componentField, errors }" name="phone_number3">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="phone_number3">
                            Phone Number 3
                          </FieldLabel>
                          <Input id="phone_number3" type="text" placeholder="Phone Number 3" v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.phone_number3']"
                            :errors="[{ message: form.errors['company.phone_number3'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField v-slot="{ componentField, errors }" name="company_size">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="company_size">
                            Company Size
                          </FieldLabel>
                          <Input id="company_size" type="number" placeholder="Company Size" v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.company_size']"
                            :errors="[{ message: form.errors['company.company_size'] }]" />
                        </Field>
                      </VeeField>
                    </FieldGroup>
                  </div>
                  <div class="bg-muted/30 p-4 rounded">
                    <FieldGroup>
                      <VeeField v-slot="{ componentField, errors }" name="contact_person">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="contact_person">
                            Contact Person
                          </FieldLabel>
                          <Input id="contact_person" type="text" placeholder="Contact Person" v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.contact_person']"
                            :errors="[{ message: form.errors['company.contact_person'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField v-slot="{ componentField, errors }" name="island_group">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="island_group">
                            Island Group
                          </FieldLabel>
                          <Select v-bind="componentField" id="island_group">
                            <SelectTrigger class="w-full">
                              <SelectValue placeholder="Select an island group" />
                            </SelectTrigger>
                            <SelectContent>
                              <SelectItem v-for="item in islandGroups" :key="item.id" :value="item.id">{{ item.label }}
                              </SelectItem>
                            </SelectContent>
                          </Select>
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.island_group']"
                            :errors="[{ message: form.errors['company.island_group'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField v-slot="{ componentField, errors }" name="notes">
                        <Field :data-invalid="!!errors.length">
                          <FieldLabel for="notes">
                            Notes
                          </FieldLabel>
                          <Textarea id="notes" placeholder="Notes" v-bind="componentField" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors.notes" :errors="[{ message: form.errors.notes }]" />
                        </Field>
                      </VeeField>
                    </FieldGroup>
                  </div>


                </div>
              </form>

              <DialogFooter>
                <Button type="submit" form="dialogForm" :disabled="isSubmitting">
                  {{ isSubmitting ? 'Saving...' : 'Create Prospect' }}
                </Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>
        </Form>

        <!-- Update -->
        <Form v-slot="{ handleSubmit }" as="" :validation-schema="formSchemaEdit">
          <Dialog v-model:open="openEdit">
            <DialogContent class="w-full max-w-4xl max-h-[80vh] overflow-y-auto">
              <DialogHeader>
                <DialogTitle>Update Prospect</DialogTitle>
                <div class="grid grid-cols-3 gap-4 h-full">
                  <div class="p-4 rounded">
                    <FieldGroup>
                      <DialogDescription>
                        <div> Prospect ID: {{ form.id }} </div>
                        <div>Prospect Created: {{ dayjs(form.created_at).format('YYYY-MM-DD') }} </div>
                        <div>Last Email Sent: {{ form.email_sent_at }}</div>
                        <div>Last Phone Call: {{ form.phone_called_at }}</div>

                      </DialogDescription>
                    </FieldGroup>
                  </div>
                  <div class="p-4 rounded"></div>
                  <div class="p-4 rounded">
                    <FieldGroup>
                      <VeeField name="account_type" v-model="form.account_type" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Convert Prospect</FieldLabel>
                          <Select id="account_type" v-model="form.account_type">
                            <SelectTrigger class="w-full">
                              <SelectValue/>
                            </SelectTrigger>
                            <SelectContent>
                              <SelectItem v-for="(value, key) in props.accountTypes" :key="key" :value="key">{{ value }}</SelectItem>
                            </SelectContent>
                          </Select>
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.account_type']"
                            :errors="[{ message: form.errors['company.account_type'] }]" />
                        </Field>
                      </VeeField>
                    </FieldGroup>
                  </div>
                </div>

              </DialogHeader>

              <form id="dialogForm" @submit="handleSubmit($event, updateProspect)">
                <div class="grid grid-cols-3 gap-4 h-full overflow-y-auto">
                  <div class="bg-muted/30 p-4 rounded">
                    <FieldGroup>
                      <VeeField name="company_name" v-model="form.company_name" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Company Name</FieldLabel>
                          <Input type="text" v-model="form.company_name" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <!-- Inertia backend validation errors -->
                          <FieldError v-if="form.errors['company.company_name']"
                            :errors="[{ message: form.errors['company.company_name'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField name="address" v-model="form.address" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Address</FieldLabel>
                          <Input type="text" v-model="form.address" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.address']"
                            :errors="[{ message: form.errors['company.address'] }]" />
                        </Field>

                      </VeeField>
                      <VeeField name="email_address1" v-model="form.email_address1" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Email Address 1</FieldLabel>
                          <Input type="text" v-model="form.email_address1" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.email_address1']"
                            :errors="[{ message: form.errors['company.email_address1'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField name="email_address2" v-model="form.email_address2" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Email Address 2</FieldLabel>
                          <Input type="text" v-model="form.email_address2" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.email_address2']"
                            :errors="[{ message: form.errors['company.email_address2'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField name="email_address3" v-model="form.email_address3" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Email Address 3</FieldLabel>
                          <Input type="text" v-model="form.email_address3" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.email_address3']"
                            :errors="[{ message: form.errors['company.email_address3'] }]" />
                        </Field>
                      </VeeField>
                    </FieldGroup>
                  </div>
                  <div class="bg-muted/30 p-4 rounded">
                    <FieldGroup>
                      <VeeField name="email_status" v-model="form.email_status" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Email Status</FieldLabel>
                          <Select id="email_status" v-model="form.email_status">
                            <SelectTrigger class="w-full">
                              <SelectValue placeholder="Email Status" />
                            </SelectTrigger>
                            <SelectContent>
                              <SelectItem v-for="item in props.emailStatuses" :key="item.id" :value="item.id">{{
                                item.label }}</SelectItem>
                            </SelectContent>
                          </Select>
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['prospect.email_status']"
                            :errors="[{ message: form.errors['prospect.email_status'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField name="industry" v-model="form.industry" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Industry</FieldLabel>
                          <Input type="text" v-model="form.industry" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.industry']"
                            :errors="[{ message: form.errors['company.industry'] }]" />
                        </Field>
                      </VeeField>


                      <VeeField name="phone_number1" v-model="form.phone_number1" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Phone Number 1</FieldLabel>
                          <Input type="text" v-model="form.phone_number1" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.phone_number1']"
                            :errors="[{ message: form.errors['company.phone_number1'] }]" />
                        </Field>
                      </VeeField>

                      <VeeField name="phone_number2" v-model="form.phone_number2" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Phone Number 2</FieldLabel>
                          <Input type="text" v-model="form.phone_number2" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.phone_number2']"
                            :errors="[{ message: form.errors['company.phone_number2'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField name="phone_number3" v-model="form.phone_number3" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Phone Number 3</FieldLabel>
                          <Input type="text" v-model="form.phone_number3" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.phone_number3']"
                            :errors="[{ message: form.errors['company.phone_number3'] }]" />
                        </Field>
                      </VeeField>
                    </FieldGroup>
                  </div>
                  <div class="bg-muted/30 p-4 rounded">
                    <FieldGroup>
                      <VeeField name="phone_call_status" v-model="form.phone_call_status" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Phone Call Status</FieldLabel>
                          <Select id="phone_call_status" v-model="form.phone_call_status">
                            <SelectTrigger class="w-full">
                              <SelectValue placeholder="Phone Call Status" />
                            </SelectTrigger>
                            <SelectContent>
                              <SelectItem v-for="item in props.phoneCallStatuses" :key="item.id" :value="item.id">{{
                                item.label }}</SelectItem>
                            </SelectContent>
                          </Select>
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['prospect.phone_call_status']"
                            :errors="[{ message: form.errors['prospect.phone_call_status'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField name="company_size" v-model="form.company_size" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Company Size</FieldLabel>
                          <Input type="number" v-model="form.company_size" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.company_size']"
                            :errors="[{ message: form.errors['company.company_size'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField name="contact_person" v-model="form.contact_person" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Contact Person</FieldLabel>
                          <Input v-model="form.contact_person" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.contact_person']"
                            :errors="[{ message: form.errors['company.contact_person'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField name="island_group" v-model="form.island_group" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Island Group</FieldLabel>
                          <Select id="island_group" v-model="form.island_group">
                            <SelectTrigger class="w-full">
                              <SelectValue placeholder="Island Group" />
                            </SelectTrigger>
                            <SelectContent>
                              <SelectItem v-for="item in props.islandGroups" :key="item.id" :value="item.id">{{
                                item.label }}</SelectItem>
                            </SelectContent>
                          </Select>
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.island_group']"
                            :errors="[{ message: form.errors['company.island_group'] }]" />
                        </Field>
                      </VeeField>
                      <VeeField name="notes" v-model="form.notes" v-slot="{ errors }">
                        <Field>
                          <FieldLabel>Notes</FieldLabel>
                          <Textarea id="notes" placeholder="Notes" v-model="form.notes" />
                          <FieldError v-if="errors.length" :errors="errors.map(e => ({ message: e }))" />
                          <FieldError v-if="form.errors['company.notes']"
                            :errors="[{ message: form.errors['company.notes'] }]" />
                        </Field>
                      </VeeField>
                    </FieldGroup>
                  </div>


                </div>
              </form>

              <DialogFooter>
                <Button type="submit" form="dialogForm" :disabled="isSubmitting">
                  {{ isSubmitting ? 'Updating...' : 'Update Prospect' }}
                </Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>
        </Form>

        <AlertDialog v-model:open="alertDelete">
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Delete Prospect?</AlertDialogTitle>
              <AlertDialogDescription>
                Are you sure you want to delete this Prospect ID: {{ form.id }}? This action cannot be undone.
              </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel @click="alertDelete = false">Cancel</AlertDialogCancel>
              <AlertDialogAction @click="DeleteProspect(form.id)">Delete</AlertDialogAction>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>

      </div>
      <div class="container py-8 mx-auto">
        <DataTable :columns="columns" :data="props.prospects" :meta="{ edit: handleEdit, delete: handleDelete }" />
      </div>
    </div>
  </AppLayout>
</template>
