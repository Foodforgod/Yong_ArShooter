import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import * as SkeletonUtils from 'three/addons/utils/SkeletonUtils.js';

export class EnemyManager {
    constructor(scene) {
        this.scene = scene;
        this.enemies = [];
        this.templateGltfs = []; // Stores multiple loaded models
    }

    async loadTemplates(urls) {
        if (!Array.isArray(urls)) urls = [urls];
        const loader = new GLTFLoader();

        for (const url of urls) {
            try {
                const gltf = await new Promise((resolve) => {
                    loader.load(url, (gltf) => resolve(gltf), undefined, () => resolve(null));
                });
                if (gltf) {
                    this.templateGltfs.push(gltf);
                    console.log("Loaded Enemy Template:", url);
                }
            } catch (e) {
                console.warn("Could not load enemy model:", url);
            }
        }
    }

    spawn(wave) {
        const hp = 4; // Takes 4 hits to die
        const speed = 0.08 * (1 + 0.05 * wave);
        
        let mesh;
        let mixer = null;
        let animations = {};
        let originalMaterials = new Map();

        // Randomly select one of the loaded enemy models
        const templateGltf = this.templateGltfs.length > 0 
            ? this.templateGltfs[Math.floor(Math.random() * this.templateGltfs.length)] 
            : null;

        if (templateGltf) {
            mesh = SkeletonUtils.clone(templateGltf.scene);
            mesh.scale.set(0.15, 0.15, 0.15);
            
            mesh.traverse((child) => {
                if (child.isMesh && child.material) {
                    child.material = child.material.clone();
                    originalMaterials.set(child, child.material.color.getHex());
                }
            });

            if (templateGltf.animations && templateGltf.animations.length > 0) {
                mixer = new THREE.AnimationMixer(mesh);
                templateGltf.animations.forEach(clip => {
                    animations[clip.name.trim().toLowerCase()] = clip;
                });
            }
        } else {
            const geo = new THREE.BoxGeometry(0.2, 0.4, 0.2);
            const mat = new THREE.MeshStandardMaterial({ color: 0xff3333 });
            mesh = new THREE.Mesh(geo, mat);
            originalMaterials.set(mesh, 0xff3333);
        }

        const angle = Math.random() * Math.PI * 2;
        const spawnDist = 1.5;
        mesh.position.set(Math.cos(angle) * spawnDist, 0, Math.sin(angle) * spawnDist);
        this.scene.add(mesh);

        const enemy = {
            mesh: mesh,
            hp: hp,
            maxHp: hp,
            speed: speed,
            mixer: mixer,
            animations: animations,
            originalMaterials: originalMaterials,
            flashTimer: 0,
            currentAction: null,
            isDead: false,
            isAttacking: false,
            deathTimer: 0,
            box: new THREE.Box3()
        };

        this.playAnimation(enemy, ['armature|walk', 'walk', 'armature|walk2', 'walk2', 'run'], true);
        this.enemies.push(enemy);
        return enemy;
    }

    playAnimation(enemy, possibleNames, loop = true) {
        if (!enemy.mixer || !enemy.animations) return false;
        
        let clip = null;
        for (const name of possibleNames) {
            const query = name.toLowerCase();
            const foundKey = Object.keys(enemy.animations).find(k => k.includes(query));
            if (foundKey) {
                clip = enemy.animations[foundKey];
                break;
            }
        }

        if (clip) {
            enemy.mixer.stopAllAction();
            const action = enemy.mixer.clipAction(clip);
            action.reset();
            action.fadeIn(0.1);
            if (!loop) {
                action.setLoop(THREE.LoopOnce);
                action.clampWhenFinished = true;
            } else {
                action.setLoop(THREE.LoopRepeat);
            }
            action.play();
            enemy.currentAction = action;
            return true;
        }
        return false;
    }

    flashWhite(enemy) {
        enemy.flashTimer = 0.2;
        enemy.mesh.traverse((child) => {
            if (child.isMesh && child.material) {
                child.material.color.setHex(0xffffff);
            }
        });
    }

    update(dt, playerPos, onPlayerAttack, onDistanceUpdate) {
        for (let i = this.enemies.length - 1; i >= 0; i--) {
            const e = this.enemies[i];
            
            if (e.mixer) e.mixer.update(dt);

            if (e.flashTimer > 0) {
                e.flashTimer -= dt;
                if (e.flashTimer <= 0) {
                    e.mesh.traverse((child) => {
                        if (child.isMesh && child.material && e.originalMaterials.has(child)) {
                            child.material.color.setHex(e.originalMaterials.get(child));
                        }
                    });
                }
            }

            if (e.isDead) {
                e.deathTimer -= dt;
                if (e.deathTimer <= 0) {
                    this.removeEnemy(i);
                }
                continue;
            }

            e.box.setFromObject(e.mesh);

            if (e.isAttacking) continue;

            const dir = new THREE.Vector3().subVectors(playerPos, e.mesh.position);
            const distToPlayer = e.mesh.position.distanceTo(playerPos);
            dir.y = 0;
            dir.normalize();

            if (onDistanceUpdate) {
                onDistanceUpdate(distToPlayer);
            }

            if (distToPlayer > 0.35) {
                e.mesh.position.addScaledVector(dir, e.speed * dt);
                e.mesh.lookAt(playerPos.x, e.mesh.position.y, playerPos.z);
            } else {
                if (!e.isAttacking) {
                    e.isAttacking = true;
                    this.playAnimation(e, ['armature|attack', 'attack'], false);

                    setTimeout(() => {
                        if (!e.isDead) {
                            if (onPlayerAttack) onPlayerAttack();
                        }
                        const idx = this.enemies.indexOf(e);
                        if (idx > -1) this.removeEnemy(idx);
                    }, 1000);
                }
            }
        }
    }

    triggerDeath(index) {
        const e = this.enemies[index];
        if (e.isDead) return;
        
        e.isDead = true;
        e.deathTimer = 2.0;

        const playedAnim = this.playAnimation(e, [
            'armature|die', 'die', 'armature|die2', 'die2', 
            'armature|death', 'death', 'dead', 'kill', 'fall'
        ], false);

        if (!playedAnim) {
            if (e.mixer) e.mixer.stopAllAction();
            e.mesh.rotation.x = -Math.PI / 2;
            e.mesh.position.y -= 0.1;
        }
    }

    removeEnemy(index) {
        if (!this.enemies[index]) return;
        const e = this.enemies[index];
        this.scene.remove(e.mesh);
        this.enemies.splice(index, 1);
    }

    clear() {
        this.enemies.forEach(e => this.scene.remove(e.mesh));
        this.enemies = [];
    }
}