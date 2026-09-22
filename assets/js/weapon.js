import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

export class WeaponSystem {
    constructor(camera, overlayScene) {
        this.camera = camera;
        this.overlayScene = overlayScene;
        this.weaponMesh = null;
        this.mixer = null;
        this.animations = {};
        this.activeAction = null;
        this.ammo = 30;
        this.maxAmmo = 30;
        this.isReloading = false;
        this.fireCooldown = 0.12;
        this.lastFireTime = 0;

        // Weapon switching state
        this.weaponUrls = [];
        this.currentIndex = 0;
    }

    async load(urls) {
        this.weaponUrls = Array.isArray(urls) ? urls : [urls];
        if (this.weaponUrls.length === 0) return;
        await this.loadWeaponModel(this.currentIndex);
    }

    async loadWeaponModel(index) {
        const url = this.weaponUrls[index];

        // Clean up existing weapon mesh if present
        if (this.weaponMesh) {
            this.overlayScene.remove(this.weaponMesh);
            this.weaponMesh = null;
        }
        this.mixer = null;
        this.animations = {};
        this.activeAction = null;

        return new Promise((resolve) => {
            const loader = new GLTFLoader();
            loader.load(url, (gltf) => {
                this.weaponMesh = gltf.scene;
                this.weaponMesh.scale.set(0.12, 0.12, 0.12);
                this.weaponMesh.position.set(0.0, -0.25, -0.4); 
                this.weaponMesh.rotation.set(0, Math.PI * 0.5, 0); 
                this.overlayScene.add(this.weaponMesh);

                if (gltf.animations && gltf.animations.length > 0) {
                    this.mixer = new THREE.AnimationMixer(this.weaponMesh);
                    gltf.animations.forEach((clip) => {
                        this.animations[clip.name.toLowerCase()] = clip;
                    });

                    const idleClip = this.animations['armature|idle'] || this.animations['idle'];
                    if (idleClip) {
                        const action = this.mixer.clipAction(idleClip);
                        action.setLoop(THREE.LoopRepeat);
                        action.play();
                        this.activeAction = action;
                    }
                }
                resolve();
            }, undefined, () => {
                // Fallback placeholder block if model fails to load
                const geo = new THREE.BoxGeometry(0.1, 0.1, 0.5);
                const mat = new THREE.MeshStandardMaterial({ color: 0x333333 });
                this.weaponMesh = new THREE.Mesh(geo, mat);
                this.weaponMesh.position.set(0.0, -0.2, -0.4);
                this.overlayScene.add(this.weaponMesh);
                resolve();
            });
        });
    }

    async switchWeapon() {
        if (this.weaponUrls.length <= 1) return;
        
        // Cycle to the next weapon index
        this.currentIndex = (this.currentIndex + 1) % this.weaponUrls.length;
        await this.loadWeaponModel(this.currentIndex);
        
        // Reset ammo and reload state upon switching
        this.ammo = this.maxAmmo;
        this.isReloading = false;
    }

    canFire() {
        const now = performance.now() / 1000;
        return !this.isReloading && this.ammo > 0 && (now - this.lastFireTime) >= this.fireCooldown;
    }

    fire() {
        this.lastFireTime = performance.now() / 1000;
        this.ammo--;

        const shootClip = this.animations['armature|shoot'] || this.animations['shoot'];
        if (this.mixer && shootClip) {
            const action = this.mixer.clipAction(shootClip);
            action.reset();
            action.setEffectiveWeight(1.0);
            action.setLoop(THREE.LoopOnce);
            action.clampWhenFinished = true;
            action.play();
        } else if (this.weaponMesh) {
            this.weaponMesh.position.z += 0.05;
            setTimeout(() => { if (this.weaponMesh) this.weaponMesh.position.z -= 0.05; }, 60);
        }
    }

    reload(onComplete) {
        if (this.isReloading || this.ammo === this.maxAmmo) return;
        this.isReloading = true;

        const reloadClip = this.animations['armature|reload'] || this.animations['reload'];
        if (this.mixer && reloadClip) {
            const action = this.mixer.clipAction(reloadClip);
            action.reset();
            action.setLoop(THREE.LoopOnce);
            action.clampWhenFinished = true;
            action.play();
        }

        setTimeout(() => {
            this.ammo = this.maxAmmo;
            this.isReloading = false;
            if (onComplete) onComplete();
        }, 1200);
    }

    update(dt) {
        if (this.mixer) {
            this.mixer.update(dt);
        }
    }
}